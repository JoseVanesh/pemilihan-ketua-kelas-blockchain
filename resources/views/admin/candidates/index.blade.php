@extends('layouts.admin')

@section('title', 'Kandidat')
@section('heading', 'Kandidat')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-bold text-slate-900">Daftar kandidat</h2>
            <p class="mt-1 text-xs text-slate-500">{{ $election?->title ?? 'Belum ada pemilihan' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('home') }}" target="_blank" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Pratinjau</a>
            <a href="{{ route('admin.candidates.create') }}" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Tambah kandidat</a>
        </div>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-[11px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Urut</th>
                        <th class="px-5 py-3 font-semibold">Kandidat</th>
                        <th class="px-5 py-3 font-semibold">Visi</th>
                        <th class="px-5 py-3 font-semibold">Suara</th>
                        <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($candidates as $candidate)
                        <tr class="hover:bg-slate-50/70">
                            <td class="whitespace-nowrap px-5 py-4 font-mono font-bold text-indigo-600">{{ $candidate->order_no }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($candidate->photoUrl())
                                        <img src="{{ $candidate->photoUrl() }}" alt="{{ $candidate->name }}" class="h-10 w-10 rounded-lg object-cover">
                                    @else
                                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50 text-sm font-extrabold text-indigo-600">
                                            {{ strtoupper(substr($candidate->name, 0, 1)) }}
                                        </span>
                                    @endif
                                    <span class="font-semibold text-slate-800">{{ $candidate->name }}</span>
                                </div>
                            </td>
                            <td class="max-w-xs px-5 py-4 text-xs text-slate-500">{{ $candidate->vision ? \Illuminate\Support\Str::limit($candidate->vision, 70) : '—' }}</td>
                            <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-800">{{ number_format($candidate->votes_count) }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.candidates.edit', $candidate) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">Edit</a>
                                    <form method="POST" action="{{ route('admin.candidates.destroy', $candidate) }}" onsubmit="return confirm('Hapus kandidat {{ $candidate->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-sm text-slate-400">Belum ada kandidat. Tambahkan kandidat pertama.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
