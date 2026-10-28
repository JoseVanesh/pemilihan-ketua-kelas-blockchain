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

class PagesTest extends TestCase
{
    use RefreshDatabase;

    private Election $election;

    private Candidate $candidate;

    private Student $student;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->election = Election::create([
            'title' => 'Pemilihan Uji Halaman',
            'class_name' => 'XI RPL 1',
            'status' => 'open',
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addDay(),
        ]);

        $this->candidate = $this->election->candidates()->create([
            'order_no' => 1,
            'name' => 'Kandidat Halaman',
            'vision' => null,
            'mission' => null,
        ]);

        $this->student = Student::create([
            'nis' => '2425001',
            'name' => 'Siswa Halaman',
            'class_name' => 'XI RPL 1',
            'voting_code' => 'HAL123',
        ]);

        app(BlockchainService::class)->createGenesis($this->election);

        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);
    }

    public function test_public_pages_render(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('Pemilihan Uji Halaman');
        $this->get(route('results'))->assertOk()->assertSee('Kandidat Halaman');
        $this->get(route('audit'))->assertOk()->assertSee('VALID');
        $this->get(route('verify'))->assertOk();
        $this->get(route('vote.login'))->assertOk();
    }

    public function test_vote_flow_pages_render(): void
    {
        $this->withSession([
            'vote_student_id' => $this->student->id,
            'vote_code' => $this->student->voting_code,
        ])->get(route('vote.candidates'))->assertOk()->assertSee('Kandidat Halaman');
    }

    public function test_receipt_page_renders(): void
    {
        $service = app(BlockchainService::class);

        $vote = DB::transaction(function () use ($service) {
            $vote = Vote::create([
                'election_id' => $this->election->id,
                'student_id' => $this->student->id,
                'candidate_id' => $this->candidate->id,
                'receipt_code' => strtoupper(Str::random(10)),
            ]);

            $service->appendVote($this->election, $vote, $service->voterHash($this->student));

            return $vote;
        });

        $this->get(route('vote.receipt', $vote->receipt_code))
            ->assertOk()
            ->assertSee($vote->receipt_code)
            ->assertSee('Kandidat Halaman');

        $this->get(route('verify', ['code' => $vote->receipt_code]))
            ->assertOk()
            ->assertSee('VALID');
    }

    public function test_admin_pages_render(): void
    {
        $this->actingAs($this->admin);

        $pages = [
            route('admin.dashboard'),
            route('admin.election.edit'),
            route('admin.candidates.index'),
            route('admin.candidates.create'),
            route('admin.candidates.edit', $this->candidate),
            route('admin.students.index'),
            route('admin.students.create'),
            route('admin.students.edit', $this->student),
            route('admin.students.codes'),
            route('admin.results'),
            route('admin.audit'),
        ];

        foreach ($pages as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_public_pages_render_without_election(): void
    {
        Vote::query()->delete();
        $this->election->blocks()->delete();
        $this->election->candidates()->delete();
        $this->election->delete();

        $this->get(route('home'))->assertOk();
        $this->get(route('results'))->assertOk();
        $this->get(route('audit'))->assertOk();
        $this->get(route('verify'))->assertOk();
    }
}
