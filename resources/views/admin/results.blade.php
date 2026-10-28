@extends('layouts.admin')

@section('title', 'Hasil Suara')
@section('heading', 'Hasil Suara')

@section('content')
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total suara</p>
            <p class="mt-1 text-2xl font-extrabold text-slate-900">{{ number_format($totalVotes) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Siswa terdaftar</p>
            <p class="mt-1 text-2xl font-extrabold text-slate-900">{{ number_format($totalStudents) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Partisipasi</p>
            <p class="mt-1 text-2xl font-extrabold text-slate-900">{{ $turnout }}%</p>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h2 class="text-sm font-bold text-slate-900">Rekapitulasi kandidat</h2>
            <a href="{{ route('results') }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500">Lihat versi publik</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-[11px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3 font-semibold">No</th>
                        <th class="px-5 py-3 font-semibold">Kandidat</th>
                        <th class="px-5 py-3 font-semibold">Suara</th>
                        <th class="px-5 py-3 font-semibold">Persentase</th>
                        <th class="w-1/3 px-5 py-3 font-semibold">Visual</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($candidates as $candidate)
                        @php
                            $percentage = $totalVotes > 0 ? round($candidate->votes_count / $totalVotes * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td class="px-5 py-4 font-mono font-bold text-indigo-600">{{ $candidate->order_no }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-800">{{ $candidate->name }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-800">{{ number_format($candidate->votes_count) }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $percentage }}%</td>
                            <td class="px-5 py-4">
                                <div class="h-2.5 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-cyan-400" style="width: {{ $totalVotes > 0 ? max(2, $percentage) : 2 }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-sm text-slate-400">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
