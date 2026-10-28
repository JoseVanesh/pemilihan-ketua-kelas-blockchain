<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Student;
use App\Models\User;
use App\Models\Vote;
use App\Services\BlockchainService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class VotingTest extends TestCase
{
    use RefreshDatabase;

    private Election $election;

    private Candidate $candidateA;

    private Candidate $candidateB;

    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->election = Election::create([
            'title' => 'Pemilihan Ketua Kelas Uji',
            'class_name' => 'XI RPL 1',
            'status' => 'open',
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addDay(),
        ]);

        $this->candidateA = $this->election->candidates()->create([
            'order_no' => 1,
            'name' => 'Kandidat Satu',
            'vision' => 'Visi satu',
            'mission' => 'Misi satu',
        ]);

        $this->candidateB = $this->election->candidates()->create([
            'order_no' => 2,
            'name' => 'Kandidat Dua',
            'vision' => 'Visi dua',
            'mission' => 'Misi dua',
        ]);

        $this->student = Student::create([
            'nis' => '2425001',
            'name' => 'Siswa Uji',
            'class_name' => 'XI RPL 1',
            'voting_code' => 'ABC123',
        ]);

        app(BlockchainService::class)->createGenesis($this->election);
    }

    public function test_student_can_login_with_code_and_cast_a_vote(): void
    {
        $this->get(route('vote.login'))->assertOk();

        $login = $this->post(route('vote.login.submit'), ['code' => 'abc123']);
        $login->assertRedirect(route('vote.candidates'));
        $login->assertSessionHas('vote_student_id', $this->student->id);

        $this->get(route('vote.candidates'))
            ->assertOk()
            ->assertSee('Kandidat Satu')
            ->assertSee('Kandidat Dua');

        $cast = $this->post(route('vote.cast'), ['candidate_id' => $this->candidateA->id]);

        $vote = Vote::firstOrFail();
        $cast->assertRedirect(route('vote.receipt', $vote->receipt_code));

        $this->assertDatabaseHas('votes', [
            'election_id' => $this->election->id,
            'student_id' => $this->student->id,
            'candidate_id' => $this->candidateA->id,
        ]);

        $this->assertNotNull($this->student->fresh()->voted_at);
        $this->assertSame(2, $this->election->blocks()->count());
        $this->assertTrue(app(BlockchainService::class)->verify($this->election)['valid']);

        $this->get(route('vote.receipt', $vote->receipt_code))
            ->assertOk()
            ->assertSee($vote->receipt_code)
            ->assertSee('Kandidat Satu');
    }

    public function test_student_cannot_vote_twice(): void
    {
        $this->castVoteDirectly();

        $response = $this->withSession([
            'vote_student_id' => $this->student->id,
            'vote_code' => $this->student->voting_code,
        ])->post(route('vote.cast'), ['candidate_id' => $this->candidateB->id]);

        $response->assertSessionHasErrors('vote');
        $this->assertSame(1, Vote::count());
        $this->assertSame(1, $this->election->blocks()->where('position', '>', 0)->count());
    }

    public function test_voting_code_cannot_be_reused_after_voting(): void
    {
        $this->castVoteDirectly();

        $response = $this->post(route('vote.login.submit'), ['code' => 'ABC123']);

        $response->assertSessionHasErrors('code');
        $response->assertSessionMissing('vote_student_id');
    }

    public function test_unknown_voting_code_is_rejected(): void
    {
        $this->post(route('vote.login.submit'), ['code' => 'TIDAKADA'])
            ->assertSessionHasErrors('code');
    }

    public function test_closed_election_rejects_voting(): void
    {
        $this->election->update(['status' => 'closed']);

        $this->post(route('vote.login.submit'), ['code' => 'ABC123'])
            ->assertSessionHasErrors('code');

        $response = $this->withSession([
            'vote_student_id' => $this->student->id,
            'vote_code' => $this->student->voting_code,
        ])->post(route('vote.cast'), ['candidate_id' => $this->candidateA->id]);

        $response->assertSessionHasErrors('vote');
        $this->assertSame(0, Vote::count());
    }

    public function test_nonexistent_candidate_is_rejected(): void
    {
        $response = $this->withSession([
            'vote_student_id' => $this->student->id,
            'vote_code' => $this->student->voting_code,
        ])->post(route('vote.cast'), ['candidate_id' => 99999]);

        $response->assertSessionHasErrors('candidate_id');
        $this->assertSame(0, Vote::count());
    }

    public function test_chain_is_valid_after_vote_and_detects_tampering(): void
    {
        $this->castVoteDirectly();

        $service = app(BlockchainService::class);
        $this->assertTrue($service->verify($this->election)['valid']);

        DB::table('blocks')
            ->where('election_id', $this->election->id)
            ->where('position', 1)
            ->update(['candidate_id' => $this->candidateB->id]);

        $verification = $service->verify($this->election->fresh());

        $this->assertFalse($verification['valid']);
        $this->assertNotEmpty($verification['errors']);
        $this->assertStringContainsString('tidak valid', implode(' ', $verification['errors']));
    }

    public function test_public_verification_page_marks_valid_receipt(): void
    {
        $vote = $this->castVoteDirectly();

        $this->get(route('verify', ['code' => $vote->receipt_code]))
            ->assertOk()
            ->assertSee($vote->receipt_code)
            ->assertSee('VALID')
            ->assertSee($this->candidateA->name);
    }

    public function test_admin_area_requires_authentication(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_login(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        $this->post(route('admin.login.submit'), [
            'email' => 'admin@test.com',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    private function castVoteDirectly(): Vote
    {
        $service = app(BlockchainService::class);

        return DB::transaction(function () use ($service) {
            $vote = Vote::create([
                'election_id' => $this->election->id,
                'student_id' => $this->student->id,
                'candidate_id' => $this->candidateA->id,
                'receipt_code' => strtoupper(Str::random(10)),
            ]);

            $service->appendVote($this->election, $vote, $service->voterHash($this->student));

            $this->student->forceFill(['voted_at' => now()])->save();

            return $vote;
        });
    }
}
