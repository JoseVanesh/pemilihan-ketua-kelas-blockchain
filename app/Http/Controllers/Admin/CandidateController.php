<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Election;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CandidateController extends Controller
{
    public function index(): View
    {
        $election = Election::current();
        $candidates = $election
            ? $election->candidates()->withCount('votes')->orderBy('order_no')->get()
            : collect();

        return view('admin.candidates.index', compact('election', 'candidates'));
    }

    public function create(): View
    {
        $election = Election::currentOrFail();
        $nextOrder = ((int) $election->candidates()->max('order_no')) + 1;

        return view('admin.candidates.form', [
            'election' => $election,
            'candidate' => new Candidate(['order_no' => $nextOrder]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $election = Election::currentOrFail();
        $data = $this->validated($request, $election);

        $photo = $data['photo'] ?? null;
        unset($data['photo']);

        if ($photo) {
            $data['photo_path'] = $photo->store('candidates', 'public');
        }

        $election->candidates()->create($data);

        return redirect()->route('admin.candidates.index')->with('success', 'Kandidat berhasil ditambahkan.');
    }

    public function edit(Candidate $candidate): View
    {
        $election = Election::currentOrFail();
        abort_unless($candidate->election_id === $election->id, 404);

        return view('admin.candidates.form', compact('election', 'candidate'));
    }

    public function update(Request $request, Candidate $candidate): RedirectResponse
    {
        $election = Election::currentOrFail();
        abort_unless($candidate->election_id === $election->id, 404);

        $data = $this->validated($request, $election, $candidate);

        $photo = $data['photo'] ?? null;
        unset($data['photo']);

        if ($photo) {
            if ($candidate->photo_path) {
                Storage::disk('public')->delete($candidate->photo_path);
            }
            $data['photo_path'] = $photo->store('candidates', 'public');
        }

        $candidate->update($data);

        return redirect()->route('admin.candidates.index')->with('success', 'Kandidat berhasil diperbarui.');
    }

    public function destroy(Candidate $candidate): RedirectResponse
    {
        if ($candidate->votes()->exists()) {
            return back()->with('error', 'Kandidat tidak bisa dihapus karena sudah memiliki suara.');
        }

        if ($candidate->photo_path) {
            Storage::disk('public')->delete($candidate->photo_path);
        }

        $candidate->delete();

        return redirect()->route('admin.candidates.index')->with('success', 'Kandidat berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, Election $election, ?Candidate $candidate = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'order_no' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('candidates', 'order_no')
                    ->where(fn ($query) => $query->where('election_id', $election->id))
                    ->ignore($candidate?->id),
            ],
            'vision' => ['nullable', 'string', 'max:2000'],
            'mission' => ['nullable', 'string', 'max:2000'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);
    }
}
