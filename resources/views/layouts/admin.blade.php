<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') &middot; {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
        @media print { .no-print { display: none !important; } body { background: #fff !important; color: #0f172a !important; } }
    </style>
</head>
<body class="min-h-full bg-slate-100 text-slate-800 antialiased">
<div class="flex min-h-screen">
    <aside class="no-print fixed inset-y-0 left-0 z-30 hidden w-64 flex-col border-r border-slate-800 bg-slate-950 text-slate-300 lg:flex">
        <div class="flex items-center gap-3 px-5 py-5">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-cyan-400 text-slate-950">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </span>
            <div class="leading-tight">
                <p class="text-sm font-extrabold text-white">Panel Panitia</p>
                <p class="text-[11px] text-slate-400">E-Voting Kelas</p>
            </div>
        </div>

        <nav class="mt-2 flex-1 space-y-1 px-3">
            @php
                $items = [
                    ['admin.dashboard', 'Dashboard', 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['admin.election.edit', 'Pemilihan', 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
                    ['admin.candidates.index', 'Kandidat', 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4z'],
                    ['admin.students.index', 'Siswa & Kode', 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197'],
                    ['admin.results', 'Hasil Suara', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    ['admin.audit', 'Audit Chain', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                ];
            @endphp
            @foreach ($items as [$route, $label, $icon])
                <a href="{{ route($route) }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs($route) ? 'bg-indigo-500/15 text-white ring-1 ring-inset ring-indigo-400/30' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        <div class="border-t border-white/5 p-4">
            <p class="mb-2 truncate text-xs text-slate-500">{{ auth()->user()?->email }}</p>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="w-full rounded-xl border border-white/10 px-3 py-2 text-sm font-semibold text-slate-300 transition hover:bg-white/5 hover:text-white">Keluar</button>
            </form>
            <a href="{{ route('home') }}" class="mt-2 block text-center text-xs text-slate-500 hover:text-slate-300">Lihat situs publik</a>
        </div>
    </aside>

    <div class="flex w-full flex-col lg:pl-64">
        <header class="no-print sticky top-0 z-20 border-b border-slate-200 bg-white/80 backdrop-blur">
            <div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-6">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-indigo-500">Panel Panitia</p>
                    <h1 class="text-lg font-bold text-slate-900">@yield('heading', 'Dashboard')</h1>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('home') }}" class="hidden rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 sm:block">Situs publik</a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800">Keluar</button>
                    </form>
                </div>
            </div>
            <div class="flex gap-1 overflow-x-auto px-4 pb-3 text-xs lg:hidden">
                @foreach ([
                    ['admin.dashboard', 'Dashboard'],
                    ['admin.election.edit', 'Pemilihan'],
                    ['admin.candidates.index', 'Kandidat'],
                    ['admin.students.index', 'Siswa'],
                    ['admin.results', 'Hasil'],
                    ['admin.audit', 'Audit'],
                ] as [$route, $label])
                    <a href="{{ route($route) }}" class="whitespace-nowrap rounded-lg px-3 py-1.5 font-semibold {{ request()->routeIs($route) ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $label }}</a>
                @endforeach
            </div>
        </header>

        <main class="flex-1 px-4 py-6 sm:px-6">
            @if (session('success'))
                <div class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-5 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
