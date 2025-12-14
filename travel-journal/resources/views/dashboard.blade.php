@php
    $cityData = $cityIntel['city'] ?? [];
    $intel = $cityIntel['intel'] ?? [];
    $timeIntel = $cityIntel['time'] ?? [];
    $budget = $cityIntel['budget'] ?? ($intel['budget'] ?? []);
    $emergencyContacts = $cityIntel['emergency_contacts'] ?? ($intel['emergency_numbers'] ?? []);
    $weatherChunks = $intel['weather'] ?? [];
    $transportHints = $intel['transport'] ?? [];
    $electricalInfo = $cityIntel['electrical'] ?? null;
@endphp

<x-app-layout>
    <div class="space-y-8"
         data-globe-dashboard
         data-search-endpoint="{{ route('cities.search') }}"
         data-intel-template="{{ route('cities.show', ['city' => '__slug__']) }}"
         data-initial='@json($cityIntel ?? [])'
         data-featured='@json($featuredCities ?? [])'>

        <div class="relative overflow-hidden rounded-3xl border border-white/20 bg-white/70 shadow-2xl backdrop-blur-xl dark:border-slate-800/80 dark:bg-slate-900/70">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/15 via-transparent to-sky-400/12"></div>
                @if(!empty($cityData['hero_image_url']))
                    <img src="{{ $cityData['hero_image_url'] }}" alt="{{ $cityData['name'] ?? 'City' }}"
                         class="absolute inset-0 h-full w-full object-cover opacity-25 dark:opacity-15" loading="lazy">
                @endif
            </div>

            <div class="relative grid gap-6 p-6 lg:grid-cols-3">
                <div class="space-y-5 lg:col-span-2">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.28em] text-indigo-600 dark:text-indigo-300">City intelligence</p>
                            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white" data-city-field="name">{{ $cityData['name'] ?? 'Pick a city' }}</h1>
                            <p class="text-sm text-slate-600 dark:text-slate-400" data-city-field="tagline">{{ $intel['tagline'] ?? 'Zoom anywhere and pull intel instantly.' }}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="pill" data-city-field="timezone">{{ $cityData['timezone'] ?? 'TZ —' }}</span>
                            <span class="pill" data-city-field="home-offset">
                                @if($timeIntel && isset($timeIntel['offset_hours']))
                                    {{ $timeIntel['offset_hours'] }}h from home
                                @else
                                    Offset pending
                                @endif
                            </span>
                            <span class="pill-primary" data-city-field="local-time">{{ isset($timeIntel['local_time']) ? \Carbon\Carbon::parse($timeIntel['local_time'])->format('H:i') : '—:—' }}</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="relative w-full max-w-xl">
                                <input type="search" name="q" autocomplete="off"
                                       value="{{ $cityData['name'] ?? '' }}"
                                       placeholder="Search city or country..."
                                       data-city-search
                                       class="w-full rounded-2xl border border-white/40 bg-white/80 px-4 py-3 text-sm text-slate-800 shadow-sm backdrop-blur focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-800 dark:bg-slate-800/80 dark:text-slate-100">
                                <div data-city-suggestions class="absolute z-30 mt-2 hidden w-full rounded-2xl border border-white/30 bg-white/95 p-1 shadow-xl backdrop-blur dark:border-slate-700 dark:bg-slate-900/95"></div>
                            </div>
                            <div class="hidden items-center gap-2 text-xs text-slate-500 dark:text-slate-400" data-city-loading>
                                <span class="h-2 w-2 animate-ping rounded-full bg-indigo-400"></span>
                                <span>Fetching intel...</span>
                            </div>
                        </div>
                        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 shadow-inner">
                            <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(96,165,250,0.18),transparent_30%),radial-gradient(circle_at_80%_0%,rgba(14,165,233,0.18),transparent_32%)]"></div>
                            <div class="relative h-[320px] w-full lg:h-[360px]" data-globe>
                                <div class="grid h-full place-items-center text-sm text-slate-300">Loading globe...</div>
                            </div>
                            <div data-globe-fallback class="absolute inset-0 hidden">
                                <img src="{{ asset('marketing/world-map.svg') }}" alt="World map fallback"
                                     class="h-full w-full object-cover opacity-50">
                                <div class="absolute inset-0 grid place-items-center bg-slate-900/60 text-xs font-semibold text-slate-100">WebGL unavailable — static map shown.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="glass-card relative overflow-hidden p-5">
                    <div class="absolute inset-0 bg-gradient-to-br from-white/40 via-transparent to-indigo-100/40 dark:from-slate-800/40 dark:to-slate-900/60"></div>
                    <div class="relative space-y-4">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">City signals</p>
                            <a href="{{ route('explore.index', ['q' => $cityData['slug'] ?? $cityData['name'] ?? '']) }}"
                               class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-300">Open Intel →</a>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-xl bg-white/70 p-3 shadow-sm ring-1 ring-white/60 backdrop-blur dark:bg-slate-800/70 dark:ring-slate-700">
                                <p class="text-xs text-slate-500 dark:text-slate-400">Local time</p>
                                <p class="text-xl font-semibold text-slate-900 dark:text-white" data-city-field="local-time">{{ isset($timeIntel['local_time']) ? \Carbon\Carbon::parse($timeIntel['local_time'])->format('H:i') : '—:—' }}</p>
                                <p class="text-[11px] text-slate-500" data-city-field="timezone">{{ $cityData['timezone'] ?? '—' }}</p>
                            </div>
                            <div class="rounded-xl bg-white/70 p-3 shadow-sm ring-1 ring-white/60 backdrop-blur dark:bg-slate-800/70 dark:ring-slate-700">
                                <p class="text-xs text-slate-500 dark:text-slate-400">Currency</p>
                                <p class="text-xl font-semibold text-slate-900 dark:text-white" data-city-field="currency">{{ $intel['currency_code'] ?? $cityData['currency_code'] ?? '—' }}</p>
                                <p class="text-[11px] text-slate-500" data-city-field="currency-rate">
                                    @if(isset($intel['currency_rate']))
                                        {{ $intel['currency_rate'] }} vs {{ $homeCurrency }}
                                    @else
                                        Spot check vs {{ $homeCurrency }}
                                    @endif
                                </p>
                            </div>
                            <div class="rounded-xl bg-white/70 p-3 shadow-sm ring-1 ring-white/60 backdrop-blur dark:bg-slate-800/70 dark:ring-slate-700">
                                <p class="text-xs text-slate-500 dark:text-slate-400">Electrical</p>
                                <p class="text-xl font-semibold text-slate-900 dark:text-white" data-city-field="electrical">{{ $electricalInfo->plug_types ?? $intel['electrical_plugs'] ?? '—' }}</p>
                                <p class="text-[11px] text-slate-500" data-city-field="voltage">{{ $electricalInfo->voltage ?? $intel['voltage'] ?? 'Voltage?' }}</p>
                            </div>
                            <div class="rounded-xl bg-white/70 p-3 shadow-sm ring-1 ring-white/60 backdrop-blur dark:bg-slate-800/70 dark:ring-slate-700">
                                <p class="text-xs text-slate-500 dark:text-slate-400">Emergency</p>
                                <ul class="space-y-1 text-sm" data-city-list="emergency">
                                    @forelse($emergencyContacts as $contact)
                                        <li class="flex items-center justify-between">
                                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $contact['label'] ?? $contact['service'] ?? 'SOS' }}</span>
                                            <span class="text-xs text-rose-500">{{ $contact['number'] ?? '' }}</span>
                                        </li>
                                    @empty
                                        <li class="text-xs text-slate-500">Add numbers</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="pill" data-city-field="visa">{{ $intel['visa'] ?? 'Visa info pending' }}</span>
                            <span class="pill">{{ $cityData['country_code'] ?? '—' }}</span>
                            <a href="{{ route('trips.index', ['q' => $cityData['name'] ?? null]) }}"
                                class="rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow hover:bg-indigo-700">
                                Plan trip with this city
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="glass-card p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Arrival primer</p>
                            <p class="text-lg font-semibold text-slate-900 dark:text-white" data-city-field="summary">{{ $intel['summary'] ?? 'High-level overview appears here.' }}</p>
                        </div>
                        <span class="pill-primary" data-city-field="currency">{{ $intel['currency_code'] ?? $cityData['currency_code'] ?? '—' }}</span>
                    </div>

                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <details class="rounded-2xl border border-white/30 bg-white/60 p-4 shadow-sm backdrop-blur dark:border-slate-800 dark:bg-slate-800/60" open>
                            <summary class="cursor-pointer text-sm font-semibold text-slate-900 dark:text-white">Neighborhoods to stay</summary>
                            <ul class="mt-3 space-y-2" data-city-list="neighborhoods">
                                @forelse($intel['neighborhoods'] ?? [] as $neighborhood)
                                    <li class="flex items-start gap-2 text-sm text-slate-800 dark:text-slate-200">
                                        <span class="mt-1 h-2 w-2 rounded-full bg-indigo-400"></span>
                                        <div>
                                            <p class="font-semibold">{{ $neighborhood['name'] ?? '' }}</p>
                                            <p class="text-xs text-slate-500">{{ $neighborhood['note'] ?? '' }}</p>
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-sm text-slate-500">Add stay ideas.</li>
                                @endforelse
                            </ul>
                        </details>
                        <details class="rounded-2xl border border-white/30 bg-white/60 p-4 shadow-sm backdrop-blur dark:border-slate-800 dark:bg-slate-800/60" open>
                            <summary class="cursor-pointer text-sm font-semibold text-slate-900 dark:text-white">Before you arrive</summary>
                            <ul class="mt-3 space-y-2" data-city-list="checklist">
                                @forelse($intel['checklist'] ?? [] as $item)
                                    <li class="flex items-start gap-2 text-sm text-slate-800 dark:text-slate-200">
                                        <span class="mt-1 h-2 w-2 rounded-full bg-emerald-400"></span>
                                        <span>{{ $item }}</span>
                                    </li>
                                @empty
                                    <li class="text-sm text-slate-500">No checklist yet.</li>
                                @endforelse
                            </ul>
                        </details>
                        <details class="rounded-2xl border border-white/30 bg-white/60 p-4 shadow-sm backdrop-blur dark:border-slate-800 dark:bg-slate-800/60">
                            <summary class="cursor-pointer text-sm font-semibold text-slate-900 dark:text-white">Cultural notes</summary>
                            <ul class="mt-3 space-y-2" data-city-list="cultural">
                                @forelse($intel['cultural_notes'] ?? [] as $note)
                                    <li class="flex items-start gap-2 text-sm text-slate-800 dark:text-slate-200">
                                        <span class="mt-1 h-2 w-2 rounded-full bg-amber-400"></span>
                                        <span>{{ $note }}</span>
                                    </li>
                                @empty
                                    <li class="text-sm text-slate-500">Add etiquette reminders.</li>
                                @endforelse
                            </ul>
                        </details>
                        <details class="rounded-2xl border border-white/30 bg-white/60 p-4 shadow-sm backdrop-blur dark:border-slate-800 dark:bg-slate-800/60">
                            <summary class="cursor-pointer text-sm font-semibold text-slate-900 dark:text-white">Weather + seasonality</summary>
                            <ul class="mt-3 space-y-2" data-city-list="weather">
                                @forelse($weatherChunks ? array_values($weatherChunks) : [] as $weather)
                                    <li class="flex items-start gap-2 text-sm text-slate-800 dark:text-slate-200">
                                        <span class="mt-1 h-2 w-2 rounded-full bg-cyan-400"></span>
                                        <span>{{ is_string($weather) ? $weather : (\Illuminate\Support\Arr::get($weather, 'snapshot') ?? '') }}</span>
                                    </li>
                                @empty
                                    <li class="text-sm text-slate-500">No weather snapshot.</li>
                                @endforelse
                            </ul>
                        </details>
                    </div>
                </div>

                <div class="glass-card p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Best months & budget</p>
                            <p class="text-lg font-semibold text-slate-900 dark:text-white">Plan the spend + timing</p>
                        </div>
                        <span class="pill">Seasonality</span>
                    </div>
                    <div class="mt-4 grid gap-4 md:grid-cols-3">
                        <div class="rounded-2xl border border-white/30 bg-white/60 p-4 backdrop-blur dark:border-slate-800 dark:bg-slate-800/60">
                            <p class="text-xs text-slate-500 dark:text-slate-400">Best months</p>
                            <ul class="mt-2 space-y-1 text-sm" data-city-list="best-months">
                                @forelse($intel['best_months'] ?? [] as $month)
                                    <li class="flex items-center gap-2">
                                        <span class="h-2 w-2 rounded-full bg-sky-400"></span>
                                        <span>{{ $month }}</span>
                                    </li>
                                @empty
                                    <li class="text-sm text-slate-500">Add season picks.</li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="rounded-2xl border border-white/30 bg-white/60 p-4 backdrop-blur dark:border-slate-800 dark:bg-slate-800/60">
                            <p class="text-xs text-slate-500 dark:text-slate-400">Daily budget</p>
                            <div class="mt-2 space-y-2 text-sm">
                                <div class="flex items-center justify-between"><span>Low</span><span class="font-semibold" data-city-field="budget-low">{{ $budget['low'] ?? '—' }}</span></div>
                                <div class="flex items-center justify-between"><span>Mid</span><span class="font-semibold" data-city-field="budget-mid">{{ $budget['mid'] ?? '—' }}</span></div>
                                <div class="flex items-center justify-between"><span>High</span><span class="font-semibold" data-city-field="budget-high">{{ $budget['high'] ?? '—' }}</span></div>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-white/30 bg-white/60 p-4 backdrop-blur dark:border-slate-800 dark:bg-slate-800/60">
                            <p class="text-xs text-slate-500 dark:text-slate-400">Transport cheat-sheet</p>
                            <ul class="mt-2 space-y-1 text-sm" data-city-list="transport">
                                @forelse($transportHints ? array_values($transportHints) : [] as $hint)
                                    <li class="flex items-start gap-2">
                                        <span class="mt-1 h-2 w-2 rounded-full bg-fuchsia-400"></span>
                                        <span>{{ $hint }}</span>
                                    </li>
                                @empty
                                    <li class="text-sm text-slate-500">Add transfers and rail tips.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="glass-card p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Intel library</h3>
                        <a href="{{ route('explore.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-300">Browse all</a>
                    </div>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach(($featuredCities ?? collect())->take(12) as $cityCard)
                            <div class="rounded-xl border border-white/20 bg-white/70 p-3 shadow-sm backdrop-blur dark:border-slate-800 dark:bg-slate-800/70">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $cityCard->name }}</p>
                                    <span class="text-[11px] text-slate-500">{{ $cityCard->timezone }}</span>
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-400">{{ $cityCard->state_region }} · {{ $cityCard->country_code }}</p>
                                <p class="mt-2 text-xs text-slate-700 dark:text-slate-300 line-clamp-2">{{ $cityCard->intel?->summary ?? 'Intel coming soon.' }}</p>
                                <div class="mt-2 flex flex-wrap gap-1 text-[11px]">
                                    @foreach(array_slice($cityCard->intel?->best_months ?? [], 0, 2) as $month)
                                        <span class="rounded-full bg-slate-100 px-2 py-1 text-slate-700 dark:bg-slate-700 dark:text-slate-100">{{ $month }}</span>
                                    @endforeach
                                    @if($cityCard->currency_code)
                                        <span class="rounded-full bg-indigo-100 px-2 py-1 text-indigo-700 dark:bg-indigo-900/60 dark:text-indigo-200">{{ $cityCard->currency_code }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="glass-card p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Active trip</p>
                            <p class="text-lg font-semibold text-slate-900 dark:text-white">{{ $currentTrip?->title ?? 'No active trip' }}</p>
                            <p class="text-xs text-slate-500">{{ $currentTrip?->location_label ?? 'Choose a trip to sync with intel.' }}</p>
                        </div>
                        @if($currentTrip)
                            <a href="{{ route('trips.show', $currentTrip) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-300">Open trip →</a>
                        @endif
                    </div>
                    <div class="mt-3 space-y-3">
                        @if($currentTrip && $timeline && $timeline->count())
                            @foreach($timeline as $item)
                                <div class="flex items-start justify-between rounded-xl bg-white/70 p-3 shadow-sm ring-1 ring-white/60 backdrop-blur dark:bg-slate-800/70 dark:ring-slate-700">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $item['title'] }}</p>
                                        <p class="text-xs text-slate-500">{{ ucfirst($item['type']) }} · {{ $item['city'] }}</p>
                                        <p class="text-xs text-slate-500">
                                            {{ optional($item['start'])->format('M d, H:i') }}
                                            @if($item['end'])
                                                → {{ optional($item['end'])->format('M d, H:i') }}
                                            @endif
                                        </p>
                                    </div>
                                    <span class="text-[11px] font-semibold text-indigo-600 dark:text-indigo-300">{{ strtoupper($item['timezone']) }}</span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-sm text-slate-500">Add itinerary items to see a timeline for your active trip.</p>
                        @endif
                    </div>
                </div>

                <div class="glass-card p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Trip notes</h3>
                        <a href="{{ route('journal.create') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-300">New entry</a>
                    </div>
                    <div class="mt-3 space-y-3">
                        @forelse($recentEntries as $entry)
                            <article class="rounded-xl bg-white/70 p-3 shadow-sm ring-1 ring-white/60 backdrop-blur dark:bg-slate-800/70 dark:ring-slate-700">
                                <div class="flex items-center justify-between text-sm text-slate-600 dark:text-slate-300">
                                    <span class="font-semibold text-slate-900 dark:text-white">{{ $entry->title }}</span>
                                    <span>{{ $entry->entry_date?->toFormattedDateString() }}</span>
                                </div>
                                <p class="mt-2 text-sm text-slate-700 dark:text-slate-200">{{ \Illuminate\Support\Str::limit($entry->body, 180) }}</p>
                                @if($entry->mood)
                                    <p class="mt-2 text-xs text-indigo-600 dark:text-indigo-300">Mood: {{ $entry->mood }}</p>
                                @endif
                            </article>
                        @empty
                            <p class="text-sm text-slate-500">No journal entries yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="glass-card p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Stats</h3>
                        <span class="pill">Year-to-date</span>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-white/70 p-4 shadow-sm ring-1 ring-white/60 backdrop-blur dark:bg-slate-800/70 dark:ring-slate-700">
                            <p class="text-xs text-slate-500 dark:text-slate-400">Trips this year</p>
                            <p class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $stats['tripsThisYear'] }}</p>
                            <p class="text-[11px] text-emerald-600 dark:text-emerald-300">On the move</p>
                        </div>
                        <div class="rounded-2xl bg-white/70 p-4 shadow-sm ring-1 ring-white/60 backdrop-blur dark:bg-slate-800/70 dark:ring-slate-700">
                            <p class="text-xs text-slate-500 dark:text-slate-400">Countries visited</p>
                            <p class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $stats['countriesVisited'] }}</p>
                            <p class="text-[11px] text-indigo-600 dark:text-indigo-300">World explorer</p>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @forelse($stats['countryCounts'] as $row)
                            <span class="pill">{{ strtoupper($row->country_code) }} · {{ $row->total }} stops</span>
                        @empty
                            <p class="text-sm text-slate-500">Log a visit to see your map fill in.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
