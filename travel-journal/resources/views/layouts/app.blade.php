<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>TripKit Atlas</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 transition-colors">
    <div class="backdrop-blur">
        <nav class="sticky top-0 z-40 border-b border-white/20 bg-white/70 backdrop-blur-xl shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
            <div class="mx-auto flex h-14 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-8">
                    <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-indigo-600 dark:text-indigo-300">TripKit</a>
                    <div class="hidden md:flex items-center gap-5 text-sm text-slate-700 dark:text-slate-300">
                        <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-300">Dashboard</a>
                        <a href="{{ route('trips.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-300">Trips</a>
                        <a href="{{ route('explore.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-300">Intel</a>
                        <a href="{{ route('journal.create') }}" class="hover:text-indigo-600 dark:hover:text-indigo-300">Trip notes</a>
                    </div>
                </div>
                <div class="flex items-center gap-3 text-sm text-slate-700 dark:text-slate-200">
                    <button type="button" data-theme-toggle
                        class="rounded-full border border-slate-200 bg-white/60 px-3 py-1 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-indigo-300 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-800/70 dark:text-slate-200 dark:hover:text-indigo-200">
                        ☀︎ / ☾
                    </button>
                    @auth
                        <div class="flex items-center gap-2">
                            <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-indigo-500 to-sky-400 text-white flex items-center justify-center text-xs font-semibold">
                                {{ strtoupper(substr(auth()->user()->display_name ?? auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden sm:block">
                                <p class="text-sm font-semibold text-slate-900 dark:text-slate-50">{{ auth()->user()->display_name ?? auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ auth()->user()->home_country ?? 'Traveler' }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="rounded-lg bg-gray-100/70 px-3 py-1 text-xs font-semibold text-gray-700 transition hover:bg-gray-200 dark:bg-slate-800/70 dark:text-slate-100 dark:hover:bg-slate-700">Logout</button>
                        </form>
                    @endauth
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            @if (isset($header))
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">{{ $header }}</h1>
                </div>
            @endif

            @if (session('status'))
                <div class="mb-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700 shadow-sm ring-1 ring-emerald-100">
                    {{ session('status') }}
                </div>
            @endif

            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>
</body>
</html>
