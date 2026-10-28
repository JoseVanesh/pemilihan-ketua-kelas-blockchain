<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->foreignId('vote_id')->nullable()->constrained()->nullOnDelete();
            $table->string('voter_hash', 64);
            $table->foreignId('candidate_id')->nullable()->constrained()->nullOnDelete();
            $table->string('previous_hash', 64);
            $table->string('hash', 64);
            $table->unsignedBigInteger('block_timestamp');
            $table->unsignedBigInteger('nonce')->default(0);
            $table->timestamps();

            $table->unique(['election_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
