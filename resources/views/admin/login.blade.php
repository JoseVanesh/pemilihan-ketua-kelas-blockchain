<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Panitia &middot; {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Plus Jakarta Sans',ui-sans-serif,system-ui,sans-serif}</style>
</head>
<body class="flex min-h-full items-center justify-center bg-slate-950 px-4 py-10 text-slate-100 antialiased">
    <div class="w-full max-w-md">
        <a href="{{ route('home') }}" class="mb-6 flex items-center justify-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-cyan-400 text-slate-950">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </span>
            <span class="leading-tight">
                <span class="block text-sm font-extrabold text-white">E-Voting Kelas</span>
                <span class="block text-[11px] text-slate-400">Panel Panitia</span>
            </span>
        </a>

        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-7 shadow-2xl shadow-black/30">
            <h1 class="text-2xl font-extrabold text-white">Masuk panel panitia</h1>
            <p class="mt-1 text-sm text-slate-400">Kelola pemilihan, kandidat, dan kode voting siswa.</p>

            @if ($errors->any())
                <div class="mt-5 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="email" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Email</label>
                    <input id="email" name="email" type="email" required autofocus value="{{ old('email') }}"
                           class="w-full rounded-xl border border-white/10 bg-slate-900/70 px-4 py-3 text-sm text-white placeholder:text-slate-600 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                </div>
                <div>
                    <label for="password" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Password</label>
                    <input id="password" name="password" type="password" required
                           class="w-full rounded-xl border border-white/10 bg-slate-900/70 px-4 py-3 text-sm text-white placeholder:text-slate-600 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                </div>
                <label class="flex items-center gap-2 text-xs text-slate-400">
                    <input type="checkbox" name="remember" value="1" class="rounded border-white/20 bg-slate-900">
                    Ingat saya
                </label>

                <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-indigo-500 to-cyan-400 px-4 py-3 text-sm font-bold text-slate-950 shadow-lg shadow-indigo-500/25 transition hover:brightness-110">
                    Masuk
                </button>
            </form>

            <div class="mt-5 rounded-xl border border-white/10 bg-slate-900/50 px-4 py-3 text-xs text-slate-400">
                Akun demo: <span class="font-semibold text-slate-200">admin@example.com</span> / <span class="font-semibold text-slate-200">password</span>
            </div>
        </div>

        <p class="mt-5 text-center text-xs text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-slate-300">&larr; Kembali ke situs publik</a>
        </p>
    </div>
</body>
</html>
