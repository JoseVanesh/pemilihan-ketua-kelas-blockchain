@extends('layouts.admin')

@section('title', 'Audit Chain')
@section('heading', 'Audit Chain')

@section('content')
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Integritas</p>
            <p class="mt-1 text-xl font-extrabold {{ $verification['valid'] ? 'text-emerald-600' : 'text-rose-600' }}">
                {{ $verification['valid'] ? 'VALID' : 'TIDAK VALID' }}
            </p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Jumlah block</p>
            <p class="mt-1 text-2xl font-extrabold text-slate-900">{{ $verification['blocks']->count() }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pemilihan</p>
            <p class="mt-1 truncate text-sm font-extrabold text-slate-900">{{ $election?->title ?? '—' }}</p>
        </div>
    </div>

    @if (! empty($verification['errors']))
        <div class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            <p class="font-bold">Masalah integritas terdeteksi:</p>
            <ul class="mt-1 list-inside list-disc space-y-1 text-xs">
                @foreach ($verification['errors'] as $issue)
                    <li>{{ $issue }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h2 class="text-sm font-bold text-slate-900">Rantai block</h2>
            <a href="{{ route('admin.audit') }}" class="rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">Verifikasi ulang</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-[10px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">#</th>
                        <th class="px-4 py-3 font-semibold">Waktu</th>
                        <th class="px-4 py-3 font-semibold">Kandidat</th>
                        <th class="px-4 py-3 font-semibold">Voter hash</th>
                        <th class="px-4 py-3 font-semibold">Hash</th>
                        <th class="px-4 py-3 font-semibold">Previous</th>
                        <th class="px-4 py-3 font-semibold">Nonce</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($verification['blocks'] as $block)
                        <tr class="hover:bg-slate-50/70">
                            <td class="whitespace-nowrap px-4 py-3 font-mono font-bold text-indigo-600">#{{ $block->position }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-xs text-slate-500">{{ $block->created_at->translatedFormat('d M Y H:i:s') }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-700">{{ $block->position === 0 ? 'Genesis' : ($block->candidate?->name ?? '—') }}</td>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-[11px] text-slate-400" title="{{ $block->voter_hash }}">{{ substr($block->voter_hash, 0, 12) }}…</td>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-[11px] text-emerald-600" title="{{ $block->hash }}">{{ substr($block->hash, 0, 12) }}…</td>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-[11px] text-slate-400" title="{{ $block->previous_hash }}">{{ substr($block->previous_hash, 0, 12) }}…</td>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-[11px] text-slate-400">{{ $block->nonce }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-sm text-slate-400">Belum ada block.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <p class="mt-4 text-xs leading-relaxed text-slate-500">
        Setiap block di-hash dengan SHA-256 dari posisi, timestamp, voter hash, kandidat, previous hash, dan nonce. Perubahan satu block akan memutus rantai dan terdeteksi pada audit.
    </p>
@endsection
