<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
        .grid-glow { background-image: radial-gradient(circle at 1px 1px, rgba(99,102,241,.14) 1px, transparent 0); background-size: 28px 28px; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body class="min-h-full bg-slate-950 text-slate-100 antialiased">
<div class="relative min-h-screen overflow-hidden">
    <div class="pointer-events-none absolute inset-0 grid-glow opacity-60"></div>
    <div class="pointer-events-none absolute -top-40 -left-32 h-96 w-96 rounded-full bg-indigo-600/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-48 right-0 h-[28rem] w-[28rem] rounded-full bg-cyan-500/10 blur-3xl"></div>

    <header class="relative z-10 border-b border-white/5 bg-slate-950/70 backdrop-blur">
        <nav class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-cyan-400 text-slate-950 shadow-lg shadow-indigo-500/30">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
                <span class="leading-tight">
                    <span class="block text-sm font-extrabold tracking-tight">E-Voting Kelas</span>
                    <span class="block text-[11px] font-medium text-slate-400">Berbasis Blockchain</span>
                </span>
            </a>

            <div class="hidden items-center gap-1 text-sm md:flex">
                @foreach ([
                    ['home', 'Beranda'],
                    ['results', 'Hasil'],
                    ['verify', 'Verifikasi'],
                    ['audit', 'Audit Chain'],
                ] as [$route, $label])
                    <a href="{{ route($route) }}"
                       class="rounded-lg px-3 py-2 font-medium transition {{ request()->routeIs($route) ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('vote.login') }}" class="rounded-xl bg-gradient-to-r from-indigo-500 to-cyan-400 px-4 py-2 text-sm font-bold text-slate-950 shadow-lg shadow-indigo-500/20 transition hover:brightness-110">
                    Vote Sekarang
                </a>
                <a href="{{ route('admin.dashboard') }}" class="rounded-xl border border-white/10 px-3 py-2 text-sm font-semibold text-slate-300 transition hover:bg-white/5 hover:text-white" title="Panel Admin">
                    Admin
                </a>
            </div>
        </nav>
        <div class="mx-auto flex max-w-6xl gap-1 overflow-x-auto px-4 pb-3 text-sm md:hidden">
            @foreach ([
                ['home', 'Beranda'],
                ['results', 'Hasil'],
                ['verify', 'Verifikasi'],
                ['audit', 'Audit Chain'],
            ] as [$route, $label])
                <a href="{{ route($route) }}" class="whitespace-nowrap rounded-lg px-3 py-1.5 font-medium {{ request()->routeIs($route) ? 'bg-white/10 text-white' : 'text-slate-400' }}">{{ $label }}</a>
            @endforeach
        </div>
    </header>

    <main class="relative z-10 mx-auto w-full max-w-6xl px-4 py-10">
        @if (session('success'))
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="relative z-10 border-t border-white/5 py-8">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-2 px-4 text-xs text-slate-500 sm:flex-row">
            <p>{{ config('app.name') }} &mdash; prototipe pemilihan dengan pencatatan hash-chain.</p>
            <p>Setiap suara direkam sebagai block yang saling terhubung.</p>
        </div>
    </footer>
</div>
</body>
</html>
