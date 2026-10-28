@extends('layouts.admin')

@section('title', 'Cetak Kode Voting')
@section('heading', 'Cetak Kode Voting')

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3 no-print">
        <div>
            <h2 class="text-sm font-bold text-slate-900">Daftar kode voting</h2>
            <p class="mt-1 text-xs text-slate-500">{{ $election?->title ?? 'Belum ada pemilihan' }} &middot; {{ $students->count() }} siswa</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.students.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali</a>
            <button onclick="window.print()" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Cetak</button>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm print:border-0 print:shadow-none">
        <div class="border-b border-slate-100 px-6 py-4 text-center">
            <h1 class="text-lg font-extrabold text-slate-900">{{ $election?->title ?? 'Pemilihan Ketua Kelas' }}</h1>
            <p class="text-xs text-slate-500">{{ $election?->class_name ?? '-' }} &mdash; Kode voting sekali-pakai</p>
        </div>
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-left text-[11px] uppercase tracking-wider text-slate-500 print:bg-white">
                <tr>
                    <th class="px-5 py-3 font-semibold">No</th>
                    <th class="px-5 py-3 font-semibold">NIS</th>
                    <th class="px-5 py-3 font-semibold">Nama</th>
                    <th class="px-5 py-3 font-semibold">Kode voting</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($students as $index => $student)
                    <tr>
                        <td class="px-5 py-2.5 text-slate-400">{{ $index + 1 }}</td>
                        <td class="px-5 py-2.5 font-mono text-xs text-slate-500">{{ $student->nis }}</td>
                        <td class="px-5 py-2.5 font-semibold text-slate-800">{{ $student->name }}</td>
                        <td class="px-5 py-2.5 font-mono text-sm font-bold tracking-[0.3em] text-slate-900">{{ $student->voting_code }}</td>
                        <td class="px-5 py-2.5 text-xs text-slate-500">{{ $student->voted_at ? 'Sudah memilih' : 'Belum memilih' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-400">Belum ada siswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
