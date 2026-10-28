<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Services\BlockchainService;
use Illuminate\Contracts\View\View;

class AuditController extends Controller
{
    public function __construct(private readonly BlockchainService $blockchain) {}

    public function index(): View
    {
        $election = Election::current();

        $verification = $election
            ? $this->blockchain->verify($election)
            : ['valid' => false, 'blocks' => collect(), 'errors' => ['Belum ada pemilihan yang dibuat.']];

        return view('public.audit', compact('election', 'verification'));
    }
}
