@extends('layouts.admin')

@section('title', 'Pemilihan')
@section('heading', 'Pengaturan Pemilihan')

@section('content')
    <div class="grid gap-6 xl:grid-cols-[1.4fr_1fr]">
        <form method="POST" action="{{ route('admin.election.update') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            <h2 class="text-sm font-bold text-slate-900">Informasi pemilihan</h2>
            <p class="mt-1 text-xs text-slate-500">Atur judul, status, dan jadwal pemilihan. Status "Berlangsung" membuka akses voting.</p>

            <div class="mt-5 space-y-4">
                <div>
                    <label for="title" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Judul pemilihan</label>
                    <input id="title" name="title" type="text" required value="{{ old('title', $election->title) }}"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="class_name" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Kelas</label>
                        <input id="class_name" name="class_name" type="text" required value="{{ old('class_name', $election->class_name) }}"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    </div>
                    <div>
                        <label for="status" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Status</label>
                        <select id="status" name="status" required
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                            @foreach (['draft' => 'Draf', 'open' => 'Berlangsung', 'closed' => 'Ditutup'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $election->status) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="starts_at" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Mulai (opsional)</label>
                        <input id="starts_at" name="starts_at" type="datetime-local"
                               value="{{ old('starts_at', $election->starts_at?->format('Y-m-d\TH:i')) }}"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    </div>
                    <div>
                        <label for="ends_at" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Selesai (opsional)</label>
                        <input id="ends_at" name="ends_at" type="datetime-local"
                               value="{{ old('ends_at', $election->ends_at?->format('Y-m-d\TH:i')) }}"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end">
                <button class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Simpan pengaturan</button>
            </div>
        </form>

        <div class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900">Status saat ini</h3>
                <p class="mt-3 inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-bold {{ $election->isOpen() ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $election->isOpen() ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                    {{ $election->statusLabel() }}
                </p>
                <dl class="mt-4 space-y-2 text-xs text-slate-500">
                    <div class="flex justify-between"><dt>Genesis block</dt><dd class="font-semibold text-slate-700">{{ $election->blocks()->where('position', 0)->exists() ? 'Sudah dibuat' : 'Belum dibuat' }}</dd></div>
                    <div class="flex justify-between"><dt>Total block</dt><dd class="font-semibold text-slate-700">{{ $election->blocks()->count() }}</dd></div>
                    <div class="flex justify-between"><dt>Suara masuk</dt><dd class="font-semibold text-slate-700">{{ $election->votes()->count() }}</dd></div>
                </dl>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-xs leading-relaxed text-amber-800">
                <p class="font-bold">Catatan</p>
                <p class="mt-1">Membuka pemilihan akan membuat genesis block jika belum ada. Menutup pemilihan menghentikan voting, tetapi data chain tetap dapat diverifikasi publik.</p>
            </div>
        </div>
    </div>
@endsection
