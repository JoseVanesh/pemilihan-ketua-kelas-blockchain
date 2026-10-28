<?php

namespace App\Services;

use App\Models\Block;
use App\Models\Election;
use App\Models\Student;
use App\Models\Vote;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BlockchainService
{
    public function difficulty(): int
    {
        return max(0, (int) config('blockchain.difficulty', 0));
    }

    public function zeroHash(): string
    {
        return str_repeat('0', 64);
    }

    public function createGenesis(Election $election): Block
    {
        $existing = $election->blocks()->orderBy('position')->first();

        if ($existing) {
            return $existing;
        }

        return $this->storeBlock($election, [
            'position' => 0,
            'vote_id' => null,
            'voter_hash' => 'GENESIS',
            'candidate_id' => null,
        ], $this->zeroHash());
    }

    public function appendVote(Election $election, Vote $vote, string $voterHash): Block
    {
        return DB::transaction(function () use ($election, $vote, $voterHash) {
            $last = Block::query()
                ->where('election_id', $election->id)
                ->orderByDesc('position')
                ->lockForUpdate()
                ->first();

            if (! $last) {
                $last = $this->createGenesis($election);
            }

            return $this->storeBlock($election, [
                'position' => $last->position + 1,
                'vote_id' => $vote->id,
                'voter_hash' => $voterHash,
                'candidate_id' => $vote->candidate_id,
            ], $last->hash);
        });
    }

    protected function storeBlock(Election $election, array $data, string $previousHash): Block
    {
        $timestamp = now()->getTimestamp();

        [$nonce, $hash] = $this->mine(
            $data['position'],
            $timestamp,
            $data['voter_hash'],
            $data['candidate_id'],
            $previousHash,
        );

        return Block::create([
            'election_id' => $election->id,
            'position' => $data['position'],
            'vote_id' => $data['vote_id'],
            'voter_hash' => $data['voter_hash'],
            'candidate_id' => $data['candidate_id'],
            'previous_hash' => $previousHash,
            'hash' => $hash,
            'block_timestamp' => $timestamp,
            'nonce' => $nonce,
        ]);
    }

    /**
     * @return array{0: int, 1: string}
     */
    public function mine(int $position, int $timestamp, string $voterHash, ?int $candidateId, string $previousHash): array
    {
        $difficulty = $this->difficulty();
        $prefix = str_repeat('0', $difficulty);
        $nonce = 0;

        while (true) {
            $hash = $this->calculateHash($position, $timestamp, $voterHash, $candidateId, $previousHash, $nonce);

            if ($difficulty === 0 || str_starts_with($hash, $prefix)) {
                return [$nonce, $hash];
            }

            $nonce++;
        }
    }

    public function calculateHash(int $position, int $timestamp, string $voterHash, ?int $candidateId, string $previousHash, int $nonce): string
    {
        $payload = implode('|', [
            $position,
            $timestamp,
            $voterHash,
            $candidateId === null ? 'null' : (string) $candidateId,
            $previousHash,
            $nonce,
        ]);

        return hash('sha256', $payload);
    }

    public function voterHash(Student|int $student): string
    {
        $id = $student instanceof Student ? $student->id : $student;

        return hash_hmac('sha256', (string) $id, (string) config('app.key'));
    }

    /**
     * @return array{valid: bool, blocks: Collection<int, Block>, errors: array<int, string>}
     */
    public function verify(Election $election): array
    {
        $blocks = $election->blocks()->orderBy('position')->get();
        $errors = [];

        if ($blocks->isEmpty()) {
            return [
                'valid' => false,
                'blocks' => $blocks,
                'errors' => ['Blockchain belum dibuat untuk pemilihan ini.'],
            ];
        }

        $expectedPosition = 0;
        $previousHash = $this->zeroHash();

        foreach ($blocks as $block) {
            if ($block->position !== $expectedPosition) {
                $errors[] = "Posisi block #{$block->position} tidak berurutan (diharapkan #{$expectedPosition}).";
            }

            if ($block->previous_hash !== $previousHash) {
                $errors[] = "previous_hash block #{$block->position} tidak cocok dengan hash block sebelumnya.";
            }

            $recalculated = $this->calculateHash(
                $block->position,
                $block->block_timestamp,
                $block->voter_hash,
                $block->candidate_id,
                $block->previous_hash,
                $block->nonce,
            );

            if (! hash_equals($recalculated, $block->hash)) {
                $errors[] = "Hash block #{$block->position} tidak valid — data block telah diubah.";
            }

            $previousHash = $block->hash;
            $expectedPosition = $block->position + 1;
        }

        return [
            'valid' => $errors === [],
            'blocks' => $blocks,
            'errors' => $errors,
        ];
    }
}
