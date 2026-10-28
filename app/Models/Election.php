<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Election extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'class_name',
        'status',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class);
    }

    public static function current(): ?self
    {
        return static::query()->latest('id')->first();
    }

    public static function currentOrFail(): self
    {
        return static::current() ?? abort(404, 'Pemilihan belum dibuat.');
    }

    public function isOpen(): bool
    {
        if ($this->status !== 'open') {
            return false;
        }

        if ($this->starts_at && now()->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && now()->gt($this->ends_at)) {
            return false;
        }

        return true;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'open' => 'Berlangsung',
            'closed' => 'Ditutup',
            default => 'Draf',
        };
    }
}
