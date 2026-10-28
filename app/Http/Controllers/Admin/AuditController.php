<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        return view('admin.audit', compact('election', 'verification'));
    }
}
