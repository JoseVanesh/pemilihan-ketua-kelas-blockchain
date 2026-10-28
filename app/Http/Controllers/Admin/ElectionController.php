<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Services\BlockchainService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ElectionController extends Controller
{
    public function __construct(private readonly BlockchainService $blockchain) {}

    public function edit(): View
    {
        $election = Election::current()
            ?? Election::create([
                'title' => 'Pemilihan Ketua Kelas',
                'class_name' => '-',
                'status' => 'draft',
            ]);

        return view('admin.election', compact('election'));
    }

    public function update(Request $request): RedirectResponse
    {
        $election = Election::currentOrFail();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'class_name' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:draft,open,closed'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        $election->update($data);

        if (in_array($election->status, ['open', 'closed'], true)) {
            $this->blockchain->createGenesis($election);
        }

        return back()->with('success', 'Pengaturan pemilihan berhasil disimpan.');
    }
}
