@extends('layouts.public')

@section('title', 'Pilih Kandidat')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-indigo-300">Halo, {{ $student->name }}</p>
            <h1 class="mt-1 text-2xl font-extrabold text-white">Pilih ketua kelas</h1>
            <p class="mt-1 text-sm text-slate-400">Pilih satu kandidat, lalu tekan tombol konfirmasi. Suara tidak dapat diubah.</p>
        </div>
        <a href="{{ route('vote.logout') }}" class="rounded-xl border border-white/10 px-4 py-2 text-xs font-semibold text-slate-300 hover:bg-white/5">
            Bukan {{ $student->name }}? Ganti
        </a>
    </div>

    <form method="POST" action="{{ route('vote.cast') }}" onsubmit="return confirm('Kirim suara Anda sekarang? Suara tidak dapat diubah setelah dikonfirmasi.');" class="mt-6">
        @csrf

        <div class="grid gap-4 sm:grid-cols-2">
            @forelse ($candidates as $candidate)
                <label class="group cursor-pointer">
                    <input type="radio" name="candidate_id" value="{{ $candidate->id }}" class="peer sr-only" @checked((string) old('candidate_id') === (string) $candidate->id) required>
                    <div class="h-full rounded-2xl border border-white/10 bg-white/[0.04] p-5 transition peer-checked:border-indigo-400 peer-checked:bg-indigo-500/10 peer-checked:ring-2 peer-checked:ring-indigo-500/40 group-hover:border-white/20">
                        <div class="flex items-center gap-3">
                            @if ($candidate->photoUrl())
                                <img src="{{ $candidate->photoUrl() }}" alt="{{ $candidate->name }}" class="h-14 w-14 rounded-xl object-cover">
                            @else
                                <span class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500/30 to-cyan-400/20 text-xl font-extrabold text-indigo-100">
                                    {{ strtoupper(substr($candidate->name, 0, 1)) }}
                                </span>
                            @endif
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold uppercase tracking-wider text-indigo-300">Kandidat {{ $candidate->order_no }}</p>
                                <h2 class="truncate text-base font-bold text-white">{{ $candidate->name }}</h2>
                            </div>
                        </div>

                        <div class="mt-4 space-y-3 text-sm">
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Visi</p>
                                <p class="mt-1 text-slate-300">{{ $candidate->vision ?: '—' }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Misi</p>
                                <p class="mt-1 whitespace-pre-line text-slate-400">{{ $candidate->mission ?: '—' }}</p>
                            </div>
                        </div>
                    </div>
                </label>
            @empty
                <div class="sm:col-span-2 rounded-2xl border border-dashed border-white/10 px-6 py-12 text-center text-sm text-slate-400">
                    Belum ada kandidat pada pemilihan ini.
                </div>
            @endforelse
        </div>

        @if ($candidates->isNotEmpty())
            <div class="sticky bottom-4 mt-6 flex items-center justify-between gap-3 rounded-2xl border border-white/10 bg-slate-900/90 p-3 backdrop-blur">
                <p class="px-2 text-xs text-slate-400">Pilih tepat satu kandidat.</p>
                <button type="submit" class="rounded-xl bg-gradient-to-r from-indigo-500 to-cyan-400 px-6 py-3 text-sm font-bold text-slate-950 shadow-lg shadow-indigo-500/25 transition hover:brightness-110">
                    Konfirmasi suara
                </button>
            </div>
        @endif
    </form>
@endsection
