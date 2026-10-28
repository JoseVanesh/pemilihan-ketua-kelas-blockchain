@extends('layouts.public')

@section('title', 'Struk Suara')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-7 shadow-2xl shadow-black/30 backdrop-blur">
            <div class="flex items-center gap-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/15 text-emerald-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <div>
                    <h1 class="text-2xl font-extrabold text-white">Suara berhasil dicatat</h1>
                    <p class="text-sm text-slate-400">Simpan kode struk ini untuk verifikasi publik.</p>
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-indigo-400/20 bg-indigo-500/10 p-5 text-center">
                <p class="text-[11px] font-semibold uppercase tracking-widest text-indigo-200">Kode struk</p>
                <div class="mt-2 flex items-center justify-center gap-3">
                    <p id="receipt-code" class="font-mono text-3xl font-extrabold tracking-[0.2em] text-white">{{ $vote->receipt_code }}</p>
                    <button type="button" onclick="navigator.clipboard.writeText('{{ $vote->receipt_code }}'); this.textContent='Tersalin';"
                            class="rounded-lg border border-white/10 px-2.5 py-1 text-[11px] font-semibold text-slate-300 hover:bg-white/5">Salin</button>
                </div>
                <p class="mt-2 text-xs text-slate-400">{{ $vote->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>

            <dl class="mt-6 divide-y divide-white/5 rounded-2xl border border-white/10">
                <div class="flex items-center justify-between px-4 py-3 text-sm">
                    <dt class="text-slate-400">Pemilihan</dt>
                    <dd class="font-semibold text-white">{{ $vote->election->title }}</dd>
                </div>
                <div class="flex items-center justify-between px-4 py-3 text-sm">
                    <dt class="text-slate-400">Kandidat dipilih</dt>
                    <dd class="font-semibold text-white">{{ $vote->candidate?->name ?? '—' }}</dd>
                </div>
                @if ($vote->block)
                    <div class="flex items-center justify-between px-4 py-3 text-sm">
                        <dt class="text-slate-400">Posisi block</dt>
                        <dd class="font-mono text-white">#{{ $vote->block->position }}</dd>
                    </div>
                    <div class="px-4 py-3 text-sm">
                        <dt class="text-slate-400">Hash block</dt>
                        <dd class="mt-1 break-all font-mono text-xs text-indigo-200">{{ $vote->block->hash }}</dd>
                    </div>
                @endif
            </dl>

            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl border px-4 py-3 text-sm {{ $blockValid ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-200' : 'border-rose-500/30 bg-rose-500/10 text-rose-200' }}">
                    <p class="text-[11px] font-semibold uppercase tracking-wider">Block suara</p>
                    <p class="mt-1 font-bold">{{ $blockValid ? 'Hash valid' : 'Hash tidak valid' }}</p>
                </div>
                <div class="rounded-2xl border px-4 py-3 text-sm {{ $verification['valid'] ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-200' : 'border-rose-500/30 bg-rose-500/10 text-rose-200' }}">
                    <p class="text-[11px] font-semibold uppercase tracking-wider">Rantai blockchain</p>
                    <p class="mt-1 font-bold">{{ $verification['valid'] ? 'Terverifikasi' : 'Bermasalah' }}</p>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('verify', ['code' => $vote->receipt_code]) }}" class="rounded-xl bg-gradient-to-r from-indigo-500 to-cyan-400 px-4 py-2.5 text-sm font-bold text-slate-950 hover:brightness-110">
                    Verifikasi publik
                </a>
                <a href="{{ route('results') }}" class="rounded-xl border border-white/10 px-4 py-2.5 text-sm font-semibold text-slate-200 hover:bg-white/5">
                    Lihat hasil
                </a>
                <button onclick="window.print()" class="rounded-xl border border-white/10 px-4 py-2.5 text-sm font-semibold text-slate-200 hover:bg-white/5">
                    Cetak
                </button>
            </div>
        </div>
    </div>
@endsection
