<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'name',
        'class_name',
        'voting_code',
        'voted_at',
    ];

    protected function casts(): array
    {
        return [
            'voted_at' => 'datetime',
        ];
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function hasVoted(): bool
    {
        return ! is_null($this->voted_at);
    }
}
