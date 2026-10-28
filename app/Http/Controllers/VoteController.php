<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Student;
use App\Models\Vote;
use App\Services\BlockchainService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VoteController extends Controller
{
    public function __construct(private readonly BlockchainService $blockchain) {}

    public function showCodeForm(): View|RedirectResponse
    {
        if (session('vote_student_id')) {
            return redirect()->route('vote.candidates');
        }

        return view('public.vote-login');
    }

    public function submitCode(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:12'],
        ]);

        $election = Election::current();
        $student = Student::query()
            ->where('voting_code', strtoupper(trim($data['code'])))
            ->first();

        if (! $student) {
            throw ValidationException::withMessages(['code' => 'Kode voting tidak ditemukan.']);
        }

        if (! $election || ! $election->isOpen()) {
            throw ValidationException::withMessages(['code' => 'Pemilihan sedang tidak dibuka.']);
        }

        if ($student->voted_at) {
            throw ValidationException::withMessages(['code' => 'Kode voting ini sudah digunakan.']);
        }

        if (Vote::query()->where('election_id', $election->id)->where('student_id', $student->id)->exists()) {
            throw ValidationException::withMessages(['code' => 'Siswa ini sudah tercatat memilih pada pemilihan ini.']);
        }

        $request->session()->put('vote_student_id', $student->id);
        $request->session()->put('vote_code', $student->voting_code);

        return redirect()->route('vote.candidates');
    }

    public function candidates(): View|RedirectResponse
    {
        $student = $this->currentStudent();
        $election = Election::current();

        if (! $student) {
            return redirect()->route('vote.login')->with('error', 'Silakan masukkan kode voting terlebih dahulu.');
        }

        if (! $election || ! $election->isOpen()) {
            return redirect()->route('home')->with('error', 'Pemilihan sedang tidak dibuka.');
        }

        if ($student->voted_at) {
            return redirect()->route('results')->with('success', 'Anda sudah memberikan suara pada pemilihan ini.');
        }

        $candidates = $election->candidates()->orderBy('order_no')->get();

        return view('public.vote-candidates', compact('student', 'election', 'candidates'));
    }

    public function cast(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'candidate_id' => ['required', 'integer', 'exists:candidates,id'],
        ]);

        $studentId = $request->session()->get('vote_student_id');
        $sessionCode = $request->session()->get('vote_code');

        if (! $studentId || ! $sessionCode) {
            return redirect()->route('vote.login')->with('error', 'Sesi voting tidak ditemukan. Silakan masukkan ulang kode voting.');
        }

        $receiptCode = DB::transaction(function () use ($studentId, $sessionCode, $data, $request) {
            $student = Student::query()->whereKey($studentId)->lockForUpdate()->firstOrFail();
            $election = Election::current();

            if (! $election || ! $election->isOpen()) {
                throw ValidationException::withMessages(['vote' => 'Pemilihan sedang tidak dibuka.']);
            }

            if ($student->voting_code !== $sessionCode) {
                throw ValidationException::withMessages(['vote' => 'Sesi voting tidak valid. Silakan masukkan ulang kode voting.']);
            }

            if ($student->voted_at) {
                throw ValidationException::withMessages(['vote' => 'Anda sudah memberikan suara.']);
            }

            if (Vote::query()->where('election_id', $election->id)->where('student_id', $student->id)->exists()) {
                throw ValidationException::withMessages(['vote' => 'Suara untuk siswa ini sudah tercatat.']);
            }

            $candidate = $election->candidates()->whereKey($data['candidate_id'])->first();

            if (! $candidate) {
                throw ValidationException::withMessages(['vote' => 'Kandidat tidak valid untuk pemilihan ini.']);
            }

            $receiptCode = $this->generateReceiptCode();

            $vote = Vote::create([
                'election_id' => $election->id,
                'student_id' => $student->id,
                'candidate_id' => $candidate->id,
                'receipt_code' => $receiptCode,
                'ip_address' => $request->ip(),
            ]);

            $this->blockchain->appendVote($election, $vote, $this->blockchain->voterHash($student));

            $student->forceFill(['voted_at' => now()])->save();

            return $receiptCode;
        });

        $request->session()->forget(['vote_student_id', 'vote_code']);

        return redirect()
            ->route('vote.receipt', $receiptCode)
            ->with('success', 'Suara Anda berhasil dicatat ke blockchain.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['vote_student_id', 'vote_code']);

        return redirect()->route('vote.login')->with('success', 'Sesi voting berhasil direset.');
    }

    public function receipt(string $code): View
    {
        $vote = Vote::query()
            ->with(['candidate', 'election', 'block'])
            ->where('receipt_code', strtoupper(trim($code)))
            ->firstOrFail();

        $verification = $this->blockchain->verify($vote->election);
        $blockValid = $vote->block
            ? hash_equals(
                $this->blockchain->calculateHash(
                    $vote->block->position,
                    $vote->block->block_timestamp,
                    $vote->block->voter_hash,
                    $vote->block->candidate_id,
                    $vote->block->previous_hash,
                    $vote->block->nonce,
                ),
                $vote->block->hash,
            )
            : false;

        return view('public.receipt', compact('vote', 'verification', 'blockValid'));
    }

    private function currentStudent(): ?Student
    {
        $id = session('vote_student_id');

        return $id ? Student::find($id) : null;
    }

    private function generateReceiptCode(): string
    {
        do {
            $code = strtoupper(Str::random(10));
        } while (Vote::query()->where('receipt_code', $code)->exists());

        return $code;
    }
}
