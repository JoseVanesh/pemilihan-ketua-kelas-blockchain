@extends('layouts.public')

@section('title', 'Audit Blockchain')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-indigo-300">Transparansi</p>
            <h1 class="mt-1 text-3xl font-extrabold text-white">Audit rantai blockchain</h1>
            <p class="mt-2 text-sm text-slate-400">Sistem menghitung ulang setiap hash block dan memeriksa keterkaitannya.</p>
        </div>
        <a href="{{ route('audit') }}" class="rounded-xl bg-gradient-to-r from-indigo-500 to-cyan-400 px-4 py-2 text-xs font-bold text-slate-950 hover:brightness-110">
            Verifikasi ulang
        </a>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-5">
            <p class="text-xs text-slate-400">Status rantai</p>
            <p class="mt-1 text-lg font-extrabold {{ $verification['valid'] ? 'text-emerald-300' : 'text-rose-300' }}">
                {{ $verification['valid'] ? 'VALID' : 'TIDAK VALID' }}
            </p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-5">
            <p class="text-xs text-slate-400">Jumlah block</p>
            <p class="mt-1 text-lg font-extrabold text-white">{{ $verification['blocks']->count() }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-5">
            <p class="text-xs text-slate-400">Pemilihan</p>
            <p class="mt-1 truncate text-lg font-extrabold text-white">{{ $election?->title ?? '—' }}</p>
        </div>
    </div>

    @if (! empty($verification['errors']))
        <div class="mt-5 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($verification['errors'] as $issue)
                    <li>{{ $issue }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-6 overflow-hidden rounded-2xl border border-white/10">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/5 text-sm">
                <thead class="bg-white/[0.03] text-left text-[11px] uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="px-4 py-3 font-semibold">#</th>
                        <th class="px-4 py-3 font-semibold">Waktu</th>
                        <th class="px-4 py-3 font-semibold">Kandidat</th>
                        <th class="px-4 py-3 font-semibold">Voter hash</th>
                        <th class="px-4 py-3 font-semibold">Hash</th>
                        <th class="px-4 py-3 font-semibold">Previous hash</th>
                        <th class="px-4 py-3 font-semibold">Nonce</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 bg-slate-950/40">
                    @forelse ($verification['blocks'] as $block)
                        <tr class="hover:bg-white/[0.02]">
                            <td class="whitespace-nowrap px-4 py-3 font-mono font-bold text-indigo-200">#{{ $block->position }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-xs text-slate-300">
                                {{ \Illuminate\Support\Carbon::createFromTimestamp($block->block_timestamp)->translatedFormat('d M Y H:i:s') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-200">
                                {{ $block->position === 0 ? 'Genesis' : ($block->candidate?->name ?? '—') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-400" title="{{ $block->voter_hash }}">{{ substr($block->voter_hash, 0, 14) }}…</td>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-emerald-300" title="{{ $block->hash }}">{{ substr($block->hash, 0, 14) }}…</td>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-500" title="{{ $block->previous_hash }}">{{ substr($block->previous_hash, 0, 14) }}…</td>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-400">{{ $block->nonce }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-400">Belum ada block pada rantai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <p class="mt-4 text-xs leading-relaxed text-slate-500">
        Catatan: pada prototipe ini blockchain disimulasikan di MySQL. Hash-chain memberi bukti tamper-evident, tetapi pengguna dengan akses penuh ke database masih dapat menulis ulang seluruh rantai.
    </p>
@endsection
