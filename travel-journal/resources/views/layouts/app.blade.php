<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Treep Atlas</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700&display=swap" rel="stylesheet" />

    @php
        $gaId = config('services.google_analytics.measurement_id');
        $gaDebug = config('services.google_analytics.debug_mode');
    @endphp

    @if ($gaId && !app()->environment('testing'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', '{{ $gaId }}', { debug_mode: {{ $gaDebug ? 'true' : 'false' }} });
        </script>
    @endif

    {{-- Prefers light theme by default; respects stored preference --}}
    <script>
        (() => {
            try {
                const key = 'treep-theme';
                const stored = localStorage.getItem(key);
                const useDark = stored === 'dark';
                document.documentElement.classList.toggle('dark', useDark);
            } catch (e) {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="font-sans antialiased min-h-screen bg-white text-slate-900 transition-colors dark:bg-slate-950 dark:text-slate-100">
    <div class="backdrop-blur flex min-h-screen flex-col">
        <nav
            class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/80 backdrop-blur-xl dark:border-slate-800/70 dark:bg-slate-900/80">
            <div class="mx-auto flex h-14 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}"
                        class="text-lg font-semibold text-orange-700 dark:text-orange-300">Treep</a>
                    <span class="pill-soft hidden md:inline-flex">Flight deck</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-slate-700 dark:text-slate-200">
                    <button type="button" data-theme-toggle aria-label="Toggle theme"
                        class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-lg transition hover:border-orange-300 hover:text-orange-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-100">
                        🌙
                    </button>
                    @auth
                        <div class="flex items-center gap-2">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500/70 text-xs font-semibold text-white">
                                {{ strtoupper(substr(auth()->user()->display_name ?? auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden sm:block">
                                <p class="text-sm font-semibold text-slate-900 dark:text-slate-50">
                                    {{ auth()->user()->display_name ?? auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ auth()->user()->home_country ?? 'Traveler' }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                class="rounded-lg bg-orange-600 px-3 py-1 text-xs font-semibold text-white transition hover:bg-orange-700 dark:bg-orange-500 dark:text-slate-950 dark:hover:bg-orange-400">Logout</button>
                        </form>
                    @endauth
                </div>
            </div>
        </nav>

        @auth
            <div class="border-b border-orange-100/80 bg-white/90 backdrop-blur">
                <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-4 px-4 py-3 text-sm sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-500/10 text-lg ring-1 ring-orange-200">
                            ⏱
                        </div>
                        <div>
                            <p class="text-[11px] uppercase tracking-wide text-slate-500">Global clock</p>
                            <p class="text-sm font-semibold text-slate-900">
                                <span data-global-clock data-tz="{{ $layoutActiveTrip['timezone'] ?? $layoutTimezone ?? config('app.timezone', 'UTC') }}">
                                    {{ now()->setTimezone($layoutActiveTrip['timezone'] ?? $layoutTimezone ?? config('app.timezone', 'UTC'))->format('H:i') }}
                                </span>
                                <span class="text-xs font-normal text-slate-500">
                                    ({{ $layoutActiveTrip['timezone'] ?? $layoutTimezone ?? 'UTC' }})
                                </span>
                            </p>
                        </div>
                    </div>

                    @if($layoutWeather)
                        <div class="flex items-center gap-3 rounded-2xl bg-orange-50 px-3 py-2 text-sm text-orange-800 ring-1 ring-orange-100 dark:bg-slate-800 dark:text-orange-200 dark:ring-slate-800" data-global-weather>
                            <span class="text-xl">{{ $layoutWeather['icon'] }}</span>
                            <div>
                                <p class="text-[11px] uppercase tracking-wide text-orange-700/80 dark:text-orange-200/80">Weather · {{ $layoutWeather['location'] }}</p>
                                <p class="font-semibold">{{ $layoutWeather['temperature_f'] }}°F / {{ $layoutWeather['temperature_c'] }}°C · {{ $layoutWeather['condition'] }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="ml-auto flex flex-wrap items-center gap-3">
                        @if($layoutActiveTrip)
                            <a href="{{ $layoutActiveTrip['url'] }}" class="inline-flex items-center rounded-full bg-orange-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700">
                                Open {{ \Illuminate\Support\Str::limit($layoutActiveTrip['title'], 24) }}
                                <span class="ml-2 text-base">→</span>
                            </a>
                        @else
                            <a href="{{ route('trips.index') }}" class="inline-flex items-center rounded-full bg-orange-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700">
                                Start a trip
                            </a>
                        @endif
                        <a href="{{ route('trips.index') }}" class="inline-flex items-center gap-2 rounded-full border border-orange-200 px-4 py-2 text-sm font-semibold text-orange-700 transition hover:bg-orange-50 dark:border-slate-700 dark:text-orange-200 dark:hover:bg-slate-800">
                            Switch trip
                        </a>
                    </div>
                </div>
            </div>
        @endauth

        <main class="mx-auto flex w-full max-w-7xl flex-1 flex-col px-4 py-10 sm:px-6 lg:px-8">
            @if (isset($header))
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-50">{{ $header }}</h1>
                </div>
            @endif

            @if (session('status'))
                <div
                    class="mb-4 rounded-xl bg-orange-500/10 px-4 py-3 text-sm text-orange-800 shadow-sm ring-1 ring-orange-200 dark:text-orange-200">
                    {{ session('status') }}
                </div>
            @endif

            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>
</body>

</html>
