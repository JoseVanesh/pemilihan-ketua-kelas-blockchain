@extends('layouts.public')

@section('title', 'Masuk Voting')

@section('content')
    <div class="mx-auto max-w-md">
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-7 shadow-2xl shadow-black/30 backdrop-blur">
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-cyan-400 text-slate-950">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </span>
            <h1 class="mt-5 text-2xl font-extrabold text-white">Masukkan kode voting</h1>
            <p class="mt-2 text-sm leading-relaxed text-slate-400">
                Gunakan kode sekali-pakai yang diberikan panitia. Satu kode hanya berlaku untuk satu suara.
            </p>

            <form method="POST" action="{{ route('vote.login.submit') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="code" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Kode voting</label>
                    <input id="code" name="code" type="text" required autofocus autocomplete="off" maxlength="12"
                           value="{{ old('code') }}"
                           placeholder="Contoh: A1B2C3"
                           class="w-full rounded-xl border border-white/10 bg-slate-900/70 px-4 py-3 text-center text-lg font-bold uppercase tracking-[0.35em] text-white placeholder:tracking-normal placeholder:text-slate-600 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                </div>

                <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-indigo-500 to-cyan-400 px-4 py-3 text-sm font-bold text-slate-950 shadow-lg shadow-indigo-500/25 transition hover:brightness-110">
                    Verifikasi kode
                </button>
            </form>

            <p class="mt-5 text-center text-xs text-slate-500">
                Belum punya kode? Hubungi panitia kelas.
            </p>
        </div>
    </div>
@endsection
