@extends('layouts.admin')

@section('title', 'Siswa')
@section('heading', 'Siswa & Kode Voting')

@section('content')
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total siswa</p>
            <p class="mt-1 text-2xl font-extrabold text-slate-900">{{ number_format($totalStudents) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Sudah memilih</p>
            <p class="mt-1 text-2xl font-extrabold text-emerald-600">{{ number_format($totalVoted) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Belum memilih</p>
            <p class="mt-1 text-2xl font-extrabold text-slate-900">{{ number_format(max(0, $totalStudents - $totalVoted)) }}</p>
        </div>
    </div>

    <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.students.index') }}" class="flex w-full max-w-md items-center gap-2">
            <input name="q" type="text" value="{{ $query }}" placeholder="Cari nama, NIS, atau kode…"
                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            <button class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cari</button>
        </form>
        <div class="flex gap-2">
            <a href="{{ route('admin.students.codes') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cetak kode</a>
            <a href="{{ route('admin.students.create') }}" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Tambah siswa</a>
        </div>
    </div>

    <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-[11px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3 font-semibold">NIS</th>
                        <th class="px-5 py-3 font-semibold">Nama</th>
                        <th class="px-5 py-3 font-semibold">Kelas</th>
                        <th class="px-5 py-3 font-semibold">Kode voting</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($students as $student)
                        <tr class="hover:bg-slate-50/70">
                            <td class="whitespace-nowrap px-5 py-3 font-mono text-xs text-slate-500">{{ $student->nis }}</td>
                            <td class="px-5 py-3 font-semibold text-slate-800">{{ $student->name }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-slate-500">{{ $student->class_name }}</td>
                            <td class="whitespace-nowrap px-5 py-3">
                                <span class="rounded-lg bg-slate-100 px-2.5 py-1 font-mono text-xs font-bold tracking-widest text-slate-700">{{ $student->voting_code }}</span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-3">
                                @if ($student->voted_at)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Sudah
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-500">
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Belum
                                    </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.students.edit', $student) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">Edit</a>
                                    <form method="POST" action="{{ route('admin.students.regenerate', $student) }}" onsubmit="return confirm('Buat kode baru untuk {{ $student->name }}?');">
                                        @csrf
                                        <button class="rounded-lg border border-indigo-200 px-3 py-1.5 text-xs font-semibold text-indigo-600 hover:bg-indigo-50">Kode baru</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.students.destroy', $student) }}" onsubmit="return confirm('Hapus siswa {{ $student->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-400">Tidak ada data siswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($students->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">
                {{ $students->links() }}
            </div>
        @endif
    </div>
@endsection
