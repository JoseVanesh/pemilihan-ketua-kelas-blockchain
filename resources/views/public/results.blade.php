@extends('layouts.public')

@section('title', 'Hasil Pemilihan')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-indigo-300">Rekapitulasi</p>
            <h1 class="mt-1 text-3xl font-extrabold text-white">Hasil pemilihan</h1>
            <p class="mt-2 text-sm text-slate-400">
                {{ $election?->title ?? 'Belum ada pemilihan' }}
                @if ($election)
                    &middot; <span class="inline-flex items-center gap-1.5"><span class="h-1.5 w-1.5 rounded-full {{ $election->isOpen() ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>{{ $election->statusLabel() }}</span>
                @endif
            </p>
        </div>
        <a href="{{ route('audit') }}" class="rounded-xl border border-white/10 px-4 py-2 text-xs font-semibold text-slate-300 hover:bg-white/5">Audit chain</a>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-5">
            <p class="text-xs text-slate-400">Total suara masuk</p>
            <p class="mt-1 text-3xl font-extrabold text-white">{{ number_format($totalVotes) }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-5">
            <p class="text-xs text-slate-400">Siswa terdaftar</p>
            <p class="mt-1 text-3xl font-extrabold text-white">{{ number_format($totalStudents) }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-5">
            <p class="text-xs text-slate-400">Partisipasi</p>
            <p class="mt-1 text-3xl font-extrabold text-white">{{ $turnout }}%</p>
        </div>
    </div>

    @if ($election?->isOpen())
        <p class="mt-4 rounded-xl border border-amber-400/20 bg-amber-400/10 px-4 py-2.5 text-xs text-amber-200">
            Pemilihan masih berlangsung. Hasil di bawah bersifat sementara dan dapat berubah.
        </p>
    @endif

    <div class="mt-6 space-y-4">
        @forelse ($candidates as $candidate)
            @php
                $percentage = $totalVotes > 0 ? round($candidate->votes_count / $totalVotes * 100, 1) : 0;
                $barWidth = $totalVotes > 0 ? max(2, round($candidate->votes_count / $totalVotes * 100)) : 2;
                $isLeader = $leader && $leader->id === $candidate->id && $candidate->votes_count > 0;
            @endphp
            <div class="rounded-2xl border {{ $isLeader ? 'border-indigo-400/40 bg-indigo-500/10' : 'border-white/10 bg-white/[0.04]' }} p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        @if ($candidate->photoUrl())
                            <img src="{{ $candidate->photoUrl() }}" alt="{{ $candidate->name }}" class="h-11 w-11 rounded-xl object-cover">
                        @else
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500/30 to-cyan-400/20 text-base font-extrabold text-indigo-100">
                                {{ strtoupper(substr($candidate->name, 0, 1)) }}
                            </span>
                        @endif
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-indigo-300">Kandidat {{ $candidate->order_no }}</p>
                            <h2 class="text-sm font-bold text-white">{{ $candidate->name }}</h2>
                        </div>
                        @if ($isLeader)
                            <span class="rounded-full bg-indigo-500/20 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-indigo-200">Suara tertinggi</span>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-extrabold text-white">{{ number_format($candidate->votes_count) }} suara</p>
                        <p class="text-xs text-slate-400">{{ $percentage }}%</p>
                    </div>
                </div>
                <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-white/5">
                    <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-cyan-400" style="width: {{ $barWidth }}%"></div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-white/10 px-6 py-12 text-center text-sm text-slate-400">
                Belum ada kandidat atau pemilihan yang dibuat.
            </div>
        @endforelse
    </div>
@endsection
