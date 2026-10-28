<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Services\BlockchainService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function __construct(private readonly BlockchainService $blockchain) {}

    public function index(Request $request): View
    {
        $code = $request->query('code');
        $vote = null;
        $verification = null;
        $blockValid = null;
        $error = null;

        if (is_string($code) && trim($code) !== '') {
            $vote = Vote::query()
                ->with(['candidate', 'election', 'block'])
                ->where('receipt_code', strtoupper(trim($code)))
                ->first();

            if (! $vote) {
                $error = 'Struk dengan kode tersebut tidak ditemukan.';
            } else {
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
            }
        }

        return view('public.verify', compact('code', 'vote', 'verification', 'blockValid', 'error'));
    }
}
