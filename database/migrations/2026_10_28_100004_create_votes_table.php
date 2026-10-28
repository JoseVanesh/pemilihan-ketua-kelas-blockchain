<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('candidate_id')->constrained()->restrictOnDelete();
            $table->string('receipt_code', 20)->unique();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->unique(['election_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
