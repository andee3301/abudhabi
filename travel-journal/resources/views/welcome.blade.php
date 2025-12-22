<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Treep · Modern Travel Journal</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900">
    @php
        $marketingAssets = app(\App\Support\MarketingAssetRepository::class);
        $flagPills = [
            ['key' => 'flag_pt', 'label' => 'Lisbon · PT', 'alt' => 'Portugal flag'],
            ['key' => 'flag_jp', 'label' => 'Kyoto · JP', 'alt' => 'Japan flag'],
            ['key' => 'flag_eg', 'label' => 'Cairo · EG', 'alt' => 'Egypt flag'],
            ['key' => 'flag_us', 'label' => 'Santa Fe · US', 'alt' => 'United States flag'],
        ];
        $weatherCities = [
            ['key' => 'flag_pt', 'city' => 'Lisbon, PT', 'meta' => '21°C · Ocean breeze', 'alt' => 'Portugal flag'],
            ['key' => 'flag_jp', 'city' => 'Kyoto, JP', 'meta' => '17°C · Hanami ready', 'alt' => 'Japan flag'],
        ];
        $galleryStack = [
            ['key' => 'gallery_camera', 'alt' => 'Vintage camera on map', 'classes' => 'h-28 w-20 rounded-2xl object-cover shadow-lg ring-1 ring-white/70'],
            ['key' => 'gallery_journal', 'alt' => 'Traveler writing a journal', 'classes' => 'h-28 w-20 rounded-2xl object-cover shadow-lg ring-1 ring-white/70 -mt-6 rotate-3'],
            ['key' => 'gallery_mountain', 'alt' => 'Mountain sunset', 'classes' => 'h-28 w-20 rounded-2xl object-cover shadow-lg ring-1 ring-white/70 mt-4 -rotate-2'],
        ];
        $avatarStack = [
            ['key' => 'avatar_one', 'alt' => 'Traveler avatar illustration'],
            ['key' => 'avatar_two', 'alt' => 'Traveler avatar illustration'],
            ['key' => 'avatar_three', 'alt' => 'Traveler avatar illustration'],
        ];
        $countryHighlights = [
            ['key' => 'flag_pt', 'name' => 'Portugal', 'entries' => '8 entries', 'alt' => 'Portugal flag'],
            ['key' => 'flag_jp', 'name' => 'Japan', 'entries' => '5 entries', 'alt' => 'Japan flag'],
            ['key' => 'flag_mx', 'name' => 'Mexico', 'entries' => '3 entries', 'alt' => 'Mexico flag'],
        ];
    @endphp
    <div class="relative overflow-hidden bg-gradient-to-br from-indigo-50 via-white to-sky-50">
        <div class="pointer-events-none absolute inset-0 opacity-60">
            <div class="absolute -left-24 top-10 h-80 w-80 rounded-full bg-indigo-200/40 blur-3xl"></div>
            <div class="absolute right-0 top-0 h-72 w-72 rounded-full bg-sky-200/40 blur-3xl"></div>
        </div>

        <div class="relative z-10 mx-auto flex min-h-screen max-w-7xl flex-col px-6 py-10 lg:px-10">
            <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <span
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/80 text-xl font-semibold text-emerald-600 shadow-lg ring-1 ring-white/50">TR</span>
                    <div>
                        <p class="text-sm uppercase tracking-widest text-gray-500">Treep</p>
                        <p class="text-lg font-semibold text-gray-900">Thoughtful travel journaling</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}"
                        class="rounded-xl bg-white/80 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-white/60 hover:text-indigo-600"
                        wire:navigate>Log in</a>
                    <a href="{{ route('register') }}"
                        class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 hover:bg-indigo-700"
                        wire:navigate>Get started</a>
                </div>
            </header>

            <main class="mt-16 flex flex-1 flex-col gap-16 lg:mt-20 lg:flex-row">
                <div class="lg:w-1/2 space-y-6">
                    <p class="text-sm uppercase tracking-[0.28em] text-gray-500">Travel journal, no clutter</p>
                    <h1 class="text-4xl font-semibold leading-tight text-gray-900">Ship your trips without maps or
                        busy dashboards.</h1>
                    <p class="max-w-xl text-sm text-gray-600">Timeline-first journaling with synced media and weather
                        so you can focus on moments, not map tiles. Works great on mobile, offline-friendly, and quick
                        to
                        update.</p>

                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center rounded-2xl bg-indigo-600 px-4 py-2 font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:bg-indigo-700"
                            wire:navigate>Start journaling</a>
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center rounded-2xl bg-white/80 px-4 py-2 font-semibold text-gray-700 shadow-sm ring-1 ring-white/60 transition hover:text-indigo-600"
                            wire:navigate>Preview the app</a>
                        <span class="pill-primary">Live sync</span>
                        <span class="pill-soft">Private by default</span>
                    </div>

                    <dl class="grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl bg-white/80 p-4 shadow ring-1 ring-white/60">
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Trips tracked</dt>
                            <dd class="mt-1 text-3xl font-semibold text-gray-900">120+</dd>
                        </div>
                        <div class="rounded-2xl bg-white/80 p-4 shadow ring-1 ring-white/60">
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Journal entries</dt>
                            <dd class="mt-1 text-3xl font-semibold text-gray-900">1,450</dd>
                        </div>
                        <div class="rounded-2xl bg-white/80 p-4 shadow ring-1 ring-white/60">
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Countries logged</dt>
                            <dd class="mt-1 text-3xl font-semibold text-gray-900">34</dd>
                        </div>
                    </dl>
                </div>

                <div class="lg:w-1/2 space-y-6">
                    <div class="glass-card relative overflow-hidden p-6 text-sm text-gray-700">
                        <div class="relative space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-gray-500">Live timeline feed</p>
                                    <p class="text-xl font-semibold text-gray-900">Recent hops and weather pulls</p>
                                </div>
                                <span class="pill-primary">Updated</span>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-3">
                                @foreach ($weatherCities as $city)
                                    <div class="rounded-xl bg-white/90 p-3 shadow ring-1 ring-white/60">
                                        <p class="text-xs text-gray-500">{{ $city['city'] }}</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ $city['meta'] }}</p>
                                        <p class="text-[11px] text-emerald-600">Synced to timeline</p>
                                    </div>
                                @endforeach
                                <div class="rounded-xl bg-white/90 p-3 shadow ring-1 ring-white/60">
                                    <p class="text-xs text-gray-500">Kyoto, JP</p>
                                    <p class="text-sm font-semibold text-gray-900">Fushimi guides</p>
                                    <p class="text-[11px] text-indigo-600">Events sorted</p>
                                </div>
                            </div>

                            <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                                <span>Auto-fetches weather, flags, and location art—no maps needed.</span>
                                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700"
                                    wire:navigate>See dashboard →</a>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-3xl bg-white/90 p-4 shadow-xl ring-1 ring-white/60">
                            <p class="text-sm font-semibold text-gray-900">Media timeline</p>
                            <p class="text-xs text-gray-600">Attach scans, reels, and doodles alongside every journal
                                block.</p>
                            <div class="mt-4 flex gap-3">
                                @foreach ($galleryStack as $frame)
                                    <img src="{{ $marketingAssets->url($frame['key']) }}" alt="{{ $frame['alt'] }}"
                                        class="{{ $frame['classes'] }}" loading="lazy" decoding="async">
                                @endforeach
                            </div>
                            <div class="mt-4 flex items-center -space-x-3">
                                @foreach ($avatarStack as $avatar)
                                    <img src="{{ $marketingAssets->url($avatar['key']) }}" alt="{{ $avatar['alt'] }}"
                                        class="h-10 w-10 rounded-full border-2 border-white object-cover" loading="lazy"
                                        decoding="async">
                                @endforeach
                                <span class="ml-4 text-xs text-gray-500">Shared albums stay in sync.</span>
                            </div>
                        </div>

                        <div class="rounded-3xl bg-gradient-to-br from-indigo-600 to-sky-500 p-5 text-white shadow-xl">
                            <p class="text-sm font-semibold">Country highlights</p>
                            <p class="mt-2 text-xs text-indigo-100">Pin stamps without relying on a globe view.</p>
                            <div class="mt-4 space-y-3 text-sm">
                                @foreach ($countryHighlights as $country)
                                    <div class="flex items-center justify-between rounded-2xl bg-white/15 px-3 py-2">
                                        <span class="inline-flex items-center gap-2">
                                            <img src="{{ $marketingAssets->url($country['key']) }}"
                                                alt="{{ $country['alt'] }}" class="h-4 w-6 rounded-sm object-cover"
                                                loading="lazy" decoding="async">
                                            {{ $country['name'] }}
                                        </span>
                                        <span class="text-xs text-indigo-100">{{ $country['entries'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-6 rounded-2xl bg-white/10 p-3 text-xs text-indigo-50">
                                Built to prioritize moments and milestones over map tiles.
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <footer
                class="mt-16 flex flex-col gap-6 border-t border-white/50 py-6 text-sm text-gray-600 sm:flex-row sm:items-center sm:justify-between">
                <p>Built with Laravel {{ Illuminate\Foundation\Application::VERSION }} · PHP {{ PHP_VERSION }}</p>
                <div class="flex items-center gap-3">
                    <a href="https://github.com/andee3301" class="text-indigo-600 hover:text-indigo-700" target="_blank"
                        rel="noreferrer">GitHub</a>
                    <span class="h-1 w-1 rounded-full bg-gray-300"></span>
                    <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700" wire:navigate>Sign
                        in</a>
                    <span class="h-1 w-1 rounded-full bg-gray-300"></span>
                    <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-700" wire:navigate>Create
                        account</a>
                </div>
            </footer>
        </div>
    </div>
</body>

</html>
