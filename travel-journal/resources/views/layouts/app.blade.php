<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Treep Atlas</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700&display=swap" rel="stylesheet" />

    {{-- Prefers dark theme by default; respects stored preference --}}
    <script>
        (() => {
            try {
                const key = 'treep-theme';
                const stored = localStorage.getItem(key);
                const useDark = stored === null ? true : stored === 'dark';
                document.documentElement.classList.toggle('dark', useDark);
            } catch (e) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen bg-slate-50 text-slate-900 transition-colors dark:bg-slate-950 dark:text-slate-100">
    <div class="backdrop-blur flex min-h-screen flex-col">
        <nav class="sticky top-0 z-40 border-b border-slate-800/70 bg-slate-950/85 backdrop-blur-xl">
            <div class="mx-auto flex h-14 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-8">
                    <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-emerald-300">Treep</a>
                    <div class="hidden md:flex items-center gap-5 text-sm text-slate-200">
                        <a href="{{ route('dashboard') }}" class="hover:text-emerald-300">Dashboard</a>
                        <a href="{{ route('trips.index') }}" class="hover:text-emerald-300">Journeys</a>
                        <a href="{{ route('explore.index') }}" class="hover:text-emerald-300">Intel</a>
                        <a href="{{ route('journal.create') }}" class="hover:text-emerald-300">New note</a>
                    </div>
                </div>
                <div class="flex items-center gap-3 text-sm text-slate-200">
                    <button type="button" data-theme-toggle aria-label="Toggle theme"
                        class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-800 bg-slate-900 text-lg transition hover:border-emerald-400 hover:text-emerald-300">
                        🌙
                    </button>
                    @auth
                        <div class="flex items-center gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500/70 text-xs font-semibold text-white">
                                {{ strtoupper(substr(auth()->user()->display_name ?? auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden sm:block">
                                <p class="text-sm font-semibold text-slate-50">{{ auth()->user()->display_name ?? auth()->user()->name }}</p>
                                <p class="text-xs text-slate-400">{{ auth()->user()->home_country ?? 'Traveler' }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="rounded-lg bg-slate-900 px-3 py-1 text-xs font-semibold text-slate-100 transition hover:text-emerald-300">Logout</button>
                        </form>
                    @endauth
                </div>
            </div>
        </nav>

        <main class="mx-auto flex w-full max-w-7xl flex-1 flex-col px-4 py-10 sm:px-6 lg:px-8">
            @if (isset($header))
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-slate-50">{{ $header }}</h1>
                </div>
            @endif

            @if (session('status'))
                <div class="mb-4 rounded-xl bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200 shadow-sm ring-1 ring-emerald-500/20">
                    {{ session('status') }}
                </div>
            @endif

            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>
</body>
</html>
