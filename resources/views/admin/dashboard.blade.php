@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Status pemilihan</p>
            <p class="mt-2 text-xl font-extrabold {{ $election?->isOpen() ? 'text-emerald-600' : 'text-slate-700' }}">
                {{ $election?->statusLabel() ?? 'Belum dibuat' }}
            </p>
            <p class="mt-1 truncate text-xs text-slate-400">{{ $election?->title ?? '—' }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Siswa terdaftar</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ number_format($totalStudents) }}</p>
            <p class="mt-1 text-xs text-slate-400">Total data siswa</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Suara masuk</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ number_format($totalVotes) }}</p>
            <p class="mt-1 text-xs text-slate-400">Partisipasi {{ $turnout }}%</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Integritas chain</p>
            <p class="mt-2 text-xl font-extrabold {{ $chain && $chain['valid'] ? 'text-emerald-600' : 'text-rose-600' }}">
                {{ $chain ? ($chain['valid'] ? 'VALID' : 'TIDAK VALID') : '—' }}
            </p>
            <a href="{{ route('admin.audit') }}" class="mt-1 inline-block text-xs font-semibold text-indigo-600 hover:text-indigo-500">Buka audit &rarr;</a>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1fr_1.2fr]">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900">Perolehan suara</h2>
                <a href="{{ route('admin.results') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500">Detail</a>
            </div>
            <div class="mt-4 space-y-4">
                @forelse ($candidates as $candidate)
                    @php
                        $percentage = $totalVotes > 0 ? round($candidate->votes_count / $totalVotes * 100, 1) : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold text-slate-700">{{ $candidate->name }}</span>
                            <span class="text-xs text-slate-500">{{ $candidate->votes_count }} suara &middot; {{ $percentage }}%</span>
                        </div>
                        <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-cyan-400" style="width: {{ $totalVotes > 0 ? max(2, $percentage) : 2 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-slate-400">Belum ada kandidat.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900">Block terbaru</h2>
                <a href="{{ route('admin.audit') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500">Semua block</a>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-left text-xs">
                    <thead class="text-[10px] uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="pb-2 pr-3 font-semibold">#</th>
                            <th class="pb-2 pr-3 font-semibold">Kandidat</th>
                            <th class="pb-2 pr-3 font-semibold">Hash</th>
                            <th class="pb-2 font-semibold">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentBlocks as $block)
                            <tr>
                                <td class="py-2 pr-3 font-mono font-bold text-indigo-600">#{{ $block->position }}</td>
                                <td class="py-2 pr-3 text-slate-700">{{ $block->position === 0 ? 'Genesis' : ($block->candidate?->name ?? '—') }}</td>
                                <td class="py-2 pr-3 font-mono text-slate-400" title="{{ $block->hash }}">{{ substr($block->hash, 0, 12) }}…</td>
                                <td class="py-2 whitespace-nowrap text-slate-400">{{ $block->created_at->translatedFormat('d M H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400">Belum ada block.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ route('admin.election.edit') }}" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Kelola pemilihan</a>
        <a href="{{ route('admin.students.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kelola siswa &amp; kode</a>
        <a href="{{ route('admin.students.codes') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cetak kode voting</a>
    </div>
@endsection
