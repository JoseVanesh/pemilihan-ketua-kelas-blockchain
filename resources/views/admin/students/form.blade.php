@extends('layouts.admin')

@section('title', $student->exists ? 'Edit Siswa' : 'Tambah Siswa')
@section('heading', $student->exists ? 'Edit Siswa' : 'Tambah Siswa')

@section('content')
    <form method="POST"
          action="{{ $student->exists ? route('admin.students.update', $student) : route('admin.students.store') }}"
          class="max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        @if ($student->exists)
            @method('PUT')
        @endif

        <div class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="nis" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">NIS</label>
                    <input id="nis" name="nis" type="text" required value="{{ old('nis', $student->nis) }}"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>
                <div>
                    <label for="class_name" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Kelas</label>
                    <input id="class_name" name="class_name" type="text" required value="{{ old('class_name', $student->class_name) }}"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>
            </div>

            <div>
                <label for="name" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Nama lengkap</label>
                <input id="name" name="name" type="text" required value="{{ old('name', $student->name) }}"
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            </div>

            <div>
                <label for="voting_code" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Kode voting</label>
                <input id="voting_code" name="voting_code" type="text" maxlength="12" required value="{{ old('voting_code', $student->voting_code) }}"
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 font-mono text-sm uppercase tracking-widest focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                <p class="mt-1 text-[11px] text-slate-400">Kode harus unik. Gunakan tombol "Kode baru" untuk membuat kode acak.</p>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-5">
            <a href="{{ route('admin.students.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">&larr; Kembali</a>
            <button class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                {{ $student->exists ? 'Simpan perubahan' : 'Tambah siswa' }}
            </button>
        </div>
    </form>
@endsection
