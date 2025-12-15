@php
    $activeTrips = collect();

    if ($currentTrip) {
        $activeTrips->push($currentTrip);
    }

    $activeTrips = $activeTrips
        ->merge($upcomingTrips ?? collect())
        ->unique('id');

    $pastTrips = collect($recentTrips ?? [])
        ->filter(function ($trip) {
            $ended = $trip->end_date && $trip->end_date->isPast();

            return $trip->status === 'completed' || $ended;
        })
        ->unique('id');

    $mapPoints = collect($mapPoints ?? []);
    $marketingAssets = app(\App\Support\MarketingAssetRepository::class);
    $mapImageUrl = $marketingAssets->url('world_map_wireframe');
    $mapAlt = $marketingAssets->metadata('world_map_wireframe', 'alt') ?? 'World map wireframe';
    $mapWidth = 1280;
    $mapHeight = 640;
@endphp

<x-app-layout>
    <div class="space-y-12 flex-1">
        {{-- Page header --}}
        <header class="space-y-2">
            <p class="text-xs uppercase tracking-[0.28em] text-slate-500">Trip overview</p>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900 dark:text-slate-50">Dashboard</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Calm view of what is in motion and what you have lived.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-600 dark:text-slate-300">
                    <span class="rounded-full bg-slate-100 px-3 py-1 font-semibold ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">Journeys: {{ $stats['totalTrips'] ?? 0 }}</span>
                    <span class="rounded-full bg-slate-100 px-3 py-1 font-semibold ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">This year: {{ $stats['tripsThisYear'] ?? 0 }}</span>
                    <span class="rounded-full bg-slate-100 px-3 py-1 font-semibold ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">Countries: {{ $stats['countriesVisited'] ?? 0 }}</span>
                </div>
            </div>
        </header>

        {{-- World map pins --}}
        <section class="rounded-3xl bg-white/90 p-5 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900/70 dark:ring-slate-800/80">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">World map</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Tap a pin to jump into its journey.</p>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-700 ring-1 ring-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-700">{{ $mapPoints->count() }} locations</span>
            </div>
            <div class="relative mt-4 overflow-hidden rounded-2xl bg-slate-100 ring-1 ring-slate-200 dark:bg-slate-950 dark:ring-slate-800">
                @if($mapImageUrl)
                    <div class="relative">
                        <img src="{{ $mapImageUrl }}" alt="{{ $mapAlt }}" class="h-full w-full object-cover" loading="lazy">
                        @if($mapPoints->isNotEmpty())
                            <svg viewBox="0 0 {{ $mapWidth }} {{ $mapHeight }}" class="absolute inset-0 h-full w-full">
                                @foreach($mapPoints as $point)
                                    @if(!is_null($point['lat']) && !is_null($point['lng']))
                                        @php
                                            $x = (($point['lng'] + 180) / 360) * $mapWidth;
                                            $y = ((90 - $point['lat']) / 180) * $mapHeight;
                                        @endphp
                                        <a href="{{ $point['url'] }}" aria-label="{{ $point['title'] }}" target="_self">
                                            <circle cx="{{ $x }}" cy="{{ $y }}" r="5" class="fill-emerald-400 stroke-slate-900/80 dark:stroke-white/70" stroke-width="1.2">
                                                <title>{{ $point['title'] }}</title>
                                            </circle>
                                        </a>
                                    @endif
                                @endforeach
                            </svg>
                        @endif
                    </div>
                @else
                    <div class="grid h-64 place-items-center text-xs text-slate-500 dark:text-slate-400">Map asset missing.</div>
                @endif
            </div>
        </section>

        {{-- Active trips progress --}}
        <section class="space-y-4">
            <div class="space-y-1">
                <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Ongoing journeys</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Current and upcoming legs with a single look at momentum.</p>
            </div>

            <div class="space-y-4">
                @forelse($activeJourneys as $trip)
                    <article class="surface-card p-5 transition hover:-translate-y-1 hover:shadow-xl" data-trip-card="{{ $trip['id'] }}">
                        <div class="flex gap-4">
                            <div class="relative h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-slate-100 ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">
                                <img src="{{ $trip['image'] }}" alt="{{ $trip['title'] }} cover" class="h-full w-full object-cover" loading="lazy">
                                @if($trip['country_code'])
                                    <span class="absolute bottom-1 left-1 flex h-5 w-7 items-center justify-center overflow-hidden rounded-md ring-1 ring-slate-200 dark:ring-slate-700">
                                        <img src="{{ $trip['flag'] }}" alt="{{ $trip['country_code'] }} flag" class="h-full w-full object-cover">
                                    </span>
                                @endif
                            </div>
                            <div class="flex-1 space-y-3">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="space-y-1">
                                        <p class="text-base font-semibold text-slate-900 dark:text-slate-50 line-clamp-1">{{ $trip['title'] }}</p>
                                        <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-1">{{ $trip['city'] ?? 'Unspecified location' }}</p>
                                    </div>
                                    <div class="flex flex-col items-end gap-1 text-right">
                                        <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $trip['progress'] }}%</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ ucfirst($trip['status']) }}</p>
                                        @if($trip['mood'])
                                            <span class="rounded-full bg-emerald-500/10 px-2 py-1 text-[11px] font-semibold text-emerald-700 ring-1 ring-emerald-500/30 dark:text-emerald-200">{{ $trip['mood'] }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="h-3 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                                    <div class="h-full rounded-full bg-emerald-500" style="width: {{ $trip['progress'] }}%"></div>
                                </div>
                                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-600 dark:text-slate-400">
                                    <span>{{ optional($trip['start'])->toFormattedDateString() ?? '—' }} – {{ optional($trip['end'])->toFormattedDateString() ?? '—' }}</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-1 font-semibold text-slate-700 ring-1 ring-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-700">TZ {{ $trip['timezone'] ?? 'TBD' }}</span>
                                    <a href="{{ $trip['url'] }}" class="text-emerald-600 hover:text-emerald-500 dark:text-emerald-300 dark:hover:text-emerald-200">Open trip</a>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="text-sm text-slate-400">No journeys in motion. Start planning your next memory.</p>
                @endforelse
            </div>
        </section>

        {{-- Past trips progress --}}
        <section class="space-y-4">
            <div class="space-y-1">
                <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Completed journeys</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Recently finished trips to revisit at a glance.</p>
            </div>

            <div class="space-y-4">
                @forelse($pastJourneys as $trip)
                    <article class="surface-card p-5 transition hover:-translate-y-1 hover:shadow-xl" data-trip-card="{{ $trip['id'] }}">
                        <div class="flex gap-4">
                            <div class="relative h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-slate-100 ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">
                                <img src="{{ $trip['image'] }}" alt="{{ $trip['title'] }} cover" class="h-full w-full object-cover" loading="lazy">
                                @if($trip['country_code'])
                                    <span class="absolute bottom-1 left-1 flex h-5 w-7 items-center justify-center overflow-hidden rounded-md ring-1 ring-slate-200 dark:ring-slate-700">
                                        <img src="{{ $trip['flag'] }}" alt="{{ $trip['country_code'] }} flag" class="h-full w-full object-cover">
                                    </span>
                                @endif
                            </div>
                            <div class="flex-1 space-y-3">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="space-y-1">
                                        <p class="text-base font-semibold text-slate-900 dark:text-slate-50 line-clamp-1">{{ $trip['title'] }}</p>
                                        <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-1">{{ $trip['city'] ?? 'Unspecified location' }}</p>
                                    </div>
                                    <div class="flex flex-col items-end gap-1 text-right">
                                        <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $trip['progress'] }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Completed</p>
                                        @if($trip['mood'])
                                            <span class="rounded-full bg-emerald-500/10 px-2 py-1 text-[11px] font-semibold text-emerald-200 ring-1 ring-emerald-500/30">{{ $trip['mood'] }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="h-3 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                                    <div class="h-full rounded-full bg-emerald-600" style="width: {{ $trip['progress'] }}%"></div>
                                </div>
                                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-600 dark:text-slate-400">
                                    <span>{{ optional($trip['start'])->toFormattedDateString() ?? '—' }} – {{ optional($trip['end'])->toFormattedDateString() ?? '—' }}</span>
                                    <a href="{{ $trip['url'] }}" class="text-emerald-600 hover:text-emerald-500 dark:text-emerald-300 dark:hover:text-emerald-200">View notes</a>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="text-sm text-slate-400">Finished trips will live here once you complete them.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
