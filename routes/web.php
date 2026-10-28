<?php

use App\Http\Controllers\Admin\AuditController as AdminAuditController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ElectionController;
use App\Http\Controllers\Admin\ResultController as AdminResultController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/hasil', [ResultController::class, 'index'])->name('results');
Route::get('/verifikasi', [VerificationController::class, 'index'])->name('verify');
Route::get('/audit', [AuditController::class, 'index'])->name('audit');

Route::get('/vote', [VoteController::class, 'showCodeForm'])->name('vote.login');
Route::get('/vote/keluar', [VoteController::class, 'logout'])->name('vote.logout');
Route::post('/vote/masuk', [VoteController::class, 'submitCode'])->name('vote.login.submit');
Route::get('/vote/kandidat', [VoteController::class, 'candidates'])->name('vote.candidates');
Route::post('/vote', [VoteController::class, 'cast'])->name('vote.cast');
Route::get('/struk/{code}', [VoteController::class, 'receipt'])->name('vote.receipt');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::get('pemilihan', [ElectionController::class, 'edit'])->name('election.edit');
        Route::put('pemilihan', [ElectionController::class, 'update'])->name('election.update');

        Route::get('kandidat', [CandidateController::class, 'index'])->name('candidates.index');
        Route::get('kandidat/create', [CandidateController::class, 'create'])->name('candidates.create');
        Route::post('kandidat', [CandidateController::class, 'store'])->name('candidates.store');
        Route::get('kandidat/{candidate}/edit', [CandidateController::class, 'edit'])->name('candidates.edit');
        Route::put('kandidat/{candidate}', [CandidateController::class, 'update'])->name('candidates.update');
        Route::delete('kandidat/{candidate}', [CandidateController::class, 'destroy'])->name('candidates.destroy');

        Route::get('siswa/cetak-kode', [StudentController::class, 'codes'])->name('students.codes');
        Route::get('siswa', [StudentController::class, 'index'])->name('students.index');
        Route::get('siswa/create', [StudentController::class, 'create'])->name('students.create');
        Route::post('siswa', [StudentController::class, 'store'])->name('students.store');
        Route::get('siswa/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
        Route::put('siswa/{student}', [StudentController::class, 'update'])->name('students.update');
        Route::delete('siswa/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
        Route::post('siswa/{student}/regenerate', [StudentController::class, 'regenerateCode'])->name('students.regenerate');

        Route::get('hasil', [AdminResultController::class, 'index'])->name('results');
        Route::get('audit', [AdminAuditController::class, 'index'])->name('audit');
    });
});
