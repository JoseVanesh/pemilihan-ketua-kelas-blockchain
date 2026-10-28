<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Block extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'position',
        'vote_id',
        'voter_hash',
        'candidate_id',
        'previous_hash',
        'hash',
        'block_timestamp',
        'nonce',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'block_timestamp' => 'integer',
            'nonce' => 'integer',
        ];
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    public function vote(): BelongsTo
    {
        return $this->belongsTo(Vote::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function shortHash(): string
    {
        return substr($this->hash, 0, 16).'…';
    }
}
