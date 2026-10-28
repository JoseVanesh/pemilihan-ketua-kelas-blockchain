@extends('layouts.public')

@section('title', 'Beranda')

@section('content')
    <section class="grid gap-8 lg:grid-cols-[1.15fr_0.85fr] lg:items-center">
        <div>
            <span class="inline-flex items-center gap-2 rounded-full border border-indigo-400/30 bg-indigo-500/10 px-3 py-1 text-xs font-semibold text-indigo-200">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                Pemilihan tercatat di blockchain
            </span>
            <h1 class="mt-5 text-4xl font-extrabold leading-tight tracking-tight text-white sm:text-5xl">
                {{ $election?->title ?? 'Pemilihan Ketua Kelas' }}
            </h1>
            <p class="mt-4 max-w-xl text-base leading-relaxed text-slate-300">
                Pilih ketua kelas dengan aman. Setiap suara disimpan sebagai block ber-hash SHA-256 yang terhubung ke block sebelumnya, sehingga perubahan data dapat dideteksi.
            </p>

            <div class="mt-7 flex flex-wrap items-center gap-3">
                <a href="{{ route('vote.login') }}" class="rounded-xl bg-gradient-to-r from-indigo-500 to-cyan-400 px-5 py-3 text-sm font-bold text-slate-950 shadow-lg shadow-indigo-500/25 transition hover:brightness-110">
                    Masuk dengan kode voting
                </a>
                <a href="{{ route('results') }}" class="rounded-xl border border-white/10 px-5 py-3 text-sm font-semibold text-slate-200 transition hover:bg-white/5">
                    Lihat hasil
                </a>
            </div>

            <div class="mt-6 flex flex-wrap items-center gap-4 text-xs text-slate-400">
                <span class="inline-flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full {{ $election?->isOpen() ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>
                    Status: {{ $election?->statusLabel() ?? 'Belum dibuat' }}
                </span>
                @if ($election?->starts_at)
                    <span>Mulai {{ $election->starts_at->translatedFormat('d M Y H:i') }}</span>
                @endif
                @if ($election?->ends_at)
                    <span>Selesai {{ $election->ends_at->translatedFormat('d M Y H:i') }}</span>
                @endif
            </div>
        </div>

        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-2xl shadow-black/30 backdrop-blur">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Ringkasan</p>
                <span class="rounded-full border border-white/10 px-2.5 py-1 text-[11px] font-semibold text-slate-300">{{ $election?->class_name ?? '-' }}</span>
            </div>
            <dl class="mt-5 grid grid-cols-2 gap-4">
                <div class="rounded-2xl border border-white/5 bg-slate-900/60 p-4">
                    <dt class="text-xs text-slate-400">Total suara</dt>
                    <dd class="mt-1 text-3xl font-extrabold text-white">{{ number_format($totalVotes) }}</dd>
                </div>
                <div class="rounded-2xl border border-white/5 bg-slate-900/60 p-4">
                    <dt class="text-xs text-slate-400">Siswa terdaftar</dt>
                    <dd class="mt-1 text-3xl font-extrabold text-white">{{ number_format($totalStudents) }}</dd>
                </div>
                <div class="rounded-2xl border border-white/5 bg-slate-900/60 p-4">
                    <dt class="text-xs text-slate-400">Kandidat</dt>
                    <dd class="mt-1 text-3xl font-extrabold text-white">{{ $candidates->count() }}</dd>
                </div>
                <div class="rounded-2xl border border-white/5 bg-slate-900/60 p-4">
                    <dt class="text-xs text-slate-400">Partisipasi</dt>
                    <dd class="mt-1 text-3xl font-extrabold text-white">{{ $totalStudents > 0 ? round($totalVotes / $totalStudents * 100, 1) : 0 }}%</dd>
                </div>
            </dl>
            <a href="{{ route('audit') }}" class="mt-5 flex items-center justify-between rounded-2xl border border-indigo-400/20 bg-indigo-500/10 px-4 py-3 text-sm font-semibold text-indigo-100 transition hover:bg-indigo-500/20">
                <span>Periksa integritas blockchain</span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </section>

    <section class="mt-14">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Kandidat</h2>
                <p class="mt-1 text-sm text-slate-400">Kenali visi dan misi sebelum memilih.</p>
            </div>
            <span class="text-xs text-slate-500">{{ $candidates->count() }} kandidat</span>
        </div>

        @if ($candidates->isEmpty())
            <div class="mt-5 rounded-2xl border border-dashed border-white/10 px-6 py-10 text-center text-sm text-slate-400">
                Belum ada kandidat yang didaftarkan.
            </div>
        @else
            <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($candidates as $candidate)
                    <article class="group overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] transition hover:border-indigo-400/30 hover:bg-white/[0.06]">
                        <div class="flex items-center gap-3 border-b border-white/5 p-4">
                            @if ($candidate->photoUrl())
                                <img src="{{ $candidate->photoUrl() }}" alt="{{ $candidate->name }}" class="h-12 w-12 rounded-xl object-cover">
                            @else
                                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500/30 to-cyan-400/20 text-lg font-extrabold text-indigo-100">
                                    {{ strtoupper(substr($candidate->name, 0, 1)) }}
                                </span>
                            @endif
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold uppercase tracking-wider text-indigo-300">Kandidat {{ $candidate->order_no }}</p>
                                <h3 class="truncate text-sm font-bold text-white">{{ $candidate->name }}</h3>
                            </div>
                        </div>
                        <div class="space-y-3 p-4 text-sm">
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Visi</p>
                                <p class="mt-1 line-clamp-3 text-slate-300">{{ $candidate->vision ?: '—' }}</p>
                            </div>
                            <div class="flex items-center justify-between border-t border-white/5 pt-3 text-xs text-slate-400">
                                <span>Suara masuk</span>
                                <span class="font-bold text-white">{{ number_format($candidate->votes_count) }}</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="mt-14 grid gap-4 md:grid-cols-3">
        @foreach ([
            ['1', 'Terima kode', 'Panitia membagikan kode voting sekali-pakai untuk setiap siswa.'],
            ['2', 'Pilih kandidat', 'Masukkan kode, pilih kandidat, lalu konfirmasi suara Anda.'],
            ['3', 'Simpan struk', 'Suara dicatat sebagai block. Simpan kode struk untuk verifikasi publik.'],
        ] as [$step, $title, $description])
            <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-5">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-500/15 text-sm font-extrabold text-indigo-200">{{ $step }}</span>
                <h3 class="mt-3 text-sm font-bold text-white">{{ $title }}</h3>
                <p class="mt-1 text-sm leading-relaxed text-slate-400">{{ $description }}</p>
            </div>
        @endforeach
    </section>
@endsection
