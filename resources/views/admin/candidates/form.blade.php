@extends('layouts.admin')

@section('title', $candidate->exists ? 'Edit Kandidat' : 'Tambah Kandidat')
@section('heading', $candidate->exists ? 'Edit Kandidat' : 'Tambah Kandidat')

@section('content')
    <form method="POST"
          action="{{ $candidate->exists ? route('admin.candidates.update', $candidate) : route('admin.candidates.store') }}"
          enctype="multipart/form-data"
          class="max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        @if ($candidate->exists)
            @method('PUT')
        @endif

        <div class="grid gap-5 sm:grid-cols-[1fr_140px]">
            <div class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-[1fr_120px]">
                    <div>
                        <label for="name" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Nama kandidat</label>
                        <input id="name" name="name" type="text" required value="{{ old('name', $candidate->name) }}"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    </div>
                    <div>
                        <label for="order_no" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">No. urut</label>
                        <input id="order_no" name="order_no" type="number" min="1" required value="{{ old('order_no', $candidate->order_no) }}"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    </div>
                </div>

                <div>
                    <label for="vision" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Visi</label>
                    <textarea id="vision" name="vision" rows="3"
                              class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">{{ old('vision', $candidate->vision) }}</textarea>
                </div>

                <div>
                    <label for="mission" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Misi</label>
                    <textarea id="mission" name="mission" rows="4"
                              class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">{{ old('mission', $candidate->mission) }}</textarea>
                </div>
            </div>

            <div>
                <p class="mb-1.5 text-xs font-semibold uppercase tracking-wider text-slate-500">Foto</p>
                <div class="flex h-32 w-full items-center justify-center overflow-hidden rounded-xl border border-dashed border-slate-300 bg-slate-50">
                    @if ($candidate->photoUrl())
                        <img src="{{ $candidate->photoUrl() }}" alt="{{ $candidate->name }}" class="h-full w-full object-cover">
                    @else
                        <span class="text-xs text-slate-400">Belum ada</span>
                    @endif
                </div>
                <input id="photo" name="photo" type="file" accept="image/*" class="mt-2 block w-full text-xs text-slate-500 file:mr-2 file:rounded-lg file:border-0 file:bg-slate-900 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white">
                <p class="mt-1 text-[11px] text-slate-400">Maks. 2 MB.</p>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-5">
            <a href="{{ route('admin.candidates.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">&larr; Kembali</a>
            <button class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                {{ $candidate->exists ? 'Simpan perubahan' : 'Tambah kandidat' }}
            </button>
        </div>
    </form>
@endsection
