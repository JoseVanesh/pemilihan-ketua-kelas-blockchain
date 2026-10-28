@extends('layouts.public')

@section('title', 'Verifikasi Struk')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-indigo-300">Verifikasi publik</p>
            <h1 class="mt-1 text-3xl font-extrabold text-white">Periksa keabsahan suara</h1>
            <p class="mt-2 text-sm text-slate-400">Masukkan kode struk untuk memeriksa kandidat, hash block, dan integritas rantai.</p>
        </div>

        <form method="GET" action="{{ route('verify') }}" class="mt-6 flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/[0.04] p-5 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label for="code" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Kode struk</label>
                <input id="code" name="code" type="text" value="{{ $code }}" maxlength="20" placeholder="Contoh: A1B2C3D4E5"
                       class="w-full rounded-xl border border-white/10 bg-slate-900/70 px-4 py-3 font-mono text-sm uppercase tracking-wider text-white placeholder:text-slate-600 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
            </div>
            <button type="submit" class="rounded-xl bg-gradient-to-r from-indigo-500 to-cyan-400 px-6 py-3 text-sm font-bold text-slate-950 hover:brightness-110">
                Verifikasi
            </button>
        </form>

        @if ($error)
            <div class="mt-5 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">{{ $error }}</div>
        @endif

        @if ($vote)
            <div class="mt-6 overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04]">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/5 bg-white/[0.02] px-6 py-4">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">Struk ditemukan</p>
                        <p class="mt-1 font-mono text-lg font-bold tracking-widest text-white">{{ $vote->receipt_code }}</p>
                    </div>
                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-bold {{ $verification['valid'] && $blockValid ? 'bg-emerald-500/15 text-emerald-300' : 'bg-rose-500/15 text-rose-300' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ $verification['valid'] && $blockValid ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                        {{ $verification['valid'] && $blockValid ? 'VALID' : 'TIDAK VALID' }}
                    </span>
                </div>

                <dl class="grid gap-px bg-white/5 sm:grid-cols-2">
                    <div class="bg-slate-950/60 px-6 py-4">
                        <dt class="text-xs text-slate-400">Pemilihan</dt>
                        <dd class="mt-1 text-sm font-semibold text-white">{{ $vote->election->title }}</dd>
                    </div>
                    <div class="bg-slate-950/60 px-6 py-4">
                        <dt class="text-xs text-slate-400">Waktu dicatat</dt>
                        <dd class="mt-1 text-sm font-semibold text-white">{{ $vote->created_at->translatedFormat('d F Y, H:i') }} WIB</dd>
                    </div>
                    <div class="bg-slate-950/60 px-6 py-4">
                        <dt class="text-xs text-slate-400">Kandidat</dt>
                        <dd class="mt-1 text-sm font-semibold text-white">{{ $vote->candidate?->name ?? '—' }}</dd>
                    </div>
                    <div class="bg-slate-950/60 px-6 py-4">
                        <dt class="text-xs text-slate-400">Posisi block</dt>
                        <dd class="mt-1 font-mono text-sm font-semibold text-white">#{{ $vote->block?->position ?? '—' }}</dd>
                    </div>
                </dl>

                @if ($vote->block)
                    <div class="space-y-4 border-t border-white/5 px-6 py-5">
                        <div>
                            <p class="text-xs text-slate-400">Hash block</p>
                            <p class="mt-1 break-all font-mono text-xs text-indigo-200">{{ $vote->block->hash }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Hash block sebelumnya</p>
                            <p class="mt-1 break-all font-mono text-xs text-slate-400">{{ $vote->block->previous_hash }}</p>
                        </div>
                    </div>
                @endif

                @if (! empty($verification['errors']))
                    <div class="border-t border-rose-500/20 bg-rose-500/10 px-6 py-4">
                        <ul class="list-inside list-disc space-y-1 text-xs text-rose-200">
                            @foreach ($verification['errors'] as $issue)
                                <li>{{ $issue }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="border-t border-white/5 px-6 py-4">
                    <a href="{{ route('audit') }}" class="text-xs font-semibold text-indigo-300 hover:text-indigo-200">Lihat seluruh rantai blockchain &rarr;</a>
                </div>
            </div>
        @endif
    </div>
@endsection
