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

    // Lightweight progress indicator without extra queries
    $progressForTrip = function ($trip) {
        if (! $trip) {
            return 0;
        }

        if ($trip->status === 'completed') {
            return 100;
        }

        $start = $trip->start_date;
        $end = $trip->end_date;

        if ($start && $end) {
            $totalDays = max($start->diffInDays($end) + 1, 1);
            $now = now();
            $endpoint = $end->lt($now) ? $end : $now;
            $elapsed = $start->isFuture() ? 0 : min($totalDays, $start->diffInDays($endpoint) + 1);

            return min(100, (int) round(($elapsed / $totalDays) * 100));
        }

        if ($trip->status === 'ongoing') {
            return 60;
        }

        return 0;
    };

    // Mood is pulled from first tag when present; otherwise a calm default per status
    $moodForTrip = function ($trip) {
        if (! $trip) {
            return null;
        }

        $tagMood = collect($trip->tags ?? [])->first();

        if ($tagMood) {
            return $tagMood;
        }

        return match ($trip->status) {
            'ongoing' => '🧭 Adventurous',
            'planned' => '✨ Curious',
            'completed' => '🌧 Reflective',
            default => '🌱 Calm',
        };
    };

    $flagUrl = function ($countryCode) {
        if (! $countryCode) {
            return asset('images/placeholder.svg');
        }

        $path = 'flags/'.strtolower($countryCode).'.svg';

        return file_exists(public_path($path)) ? asset($path) : asset('images/placeholder.svg');
    };

    $mapPoints = collect($mapTrips ?? [])
        ->filter(function ($trip) {
            $lat = data_get($trip, 'city.latitude');
            $lng = data_get($trip, 'city.longitude');

            return ! is_null($lat) && ! is_null($lng);
        })
        ->unique('id')
        ->map(function ($trip) use ($moodForTrip) {
            $lat = data_get($trip, 'city.latitude');
            $lng = data_get($trip, 'city.longitude');
            $country = $trip->country_code ?? data_get($trip, 'city.country_code');

            return [
                'id' => $trip->id,
                'title' => $trip->title,
                'lat' => $lat,
                'lng' => $lng,
                'url' => route('trips.show', $trip),
                'status' => $trip->status,
                'country' => $country,
                'mood' => $moodForTrip($trip),
            ];
        })
        ->values();
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
            <div class="relative mt-4 min-h-[320px] overflow-hidden rounded-2xl bg-slate-100 ring-1 ring-slate-200 dark:bg-slate-950 dark:ring-slate-800"
                 data-world-map
                 data-map-src="{{ asset('maps/world.svg') }}"
                 data-map-points='@json($mapPoints)'>
                <div class="grid h-full place-items-center text-xs text-slate-500 dark:text-slate-400">Loading world map…</div>
            </div>
        </section>

        {{-- Active trips progress --}}
        <section class="space-y-4">
            <div class="space-y-1">
                <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Ongoing journeys</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Current and upcoming legs with a single look at momentum.</p>
            </div>

            <div class="space-y-4">
                @forelse($activeTrips as $trip)
                    @php
                        $progress = $progressForTrip($trip);
                        $flag = $flagUrl($trip->country_code ?? $trip->city?->country_code);
                    @endphp
                    <article class="rounded-3xl bg-white/90 p-5 shadow-sm ring-1 ring-slate-200 transition dark:bg-slate-900/70 dark:ring-slate-800/80" data-trip-card="{{ $trip->id }}">
                        <div class="flex gap-4">
                            <div class="relative h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-slate-100 ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">
                                <img src="{{ $trip->cover_url }}" alt="{{ $trip->title }} cover" class="h-full w-full object-cover" loading="lazy">
                                @if($trip->country_code ?? $trip->city?->country_code)
                                    <span class="absolute bottom-1 left-1 flex h-5 w-7 items-center justify-center overflow-hidden rounded-md ring-1 ring-slate-200 dark:ring-slate-700">
                                        <img src="{{ $flag }}" alt="{{ $trip->country_code ?? $trip->city?->country_code }} flag" class="h-full w-full object-cover">
                                    </span>
                                @endif
                            </div>
                            <div class="flex-1 space-y-3">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="space-y-1">
                                        <p class="text-base font-semibold text-slate-900 dark:text-slate-50 line-clamp-1">{{ $trip->title }}</p>
                                        <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-1">{{ $trip->location_label }}</p>
                                    </div>
                                    <div class="flex flex-col items-end gap-1 text-right">
                                        <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $progress }}%</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ ucfirst($trip->status) }}</p>
                                        @if($mood = $moodForTrip($trip))
                                            <span class="rounded-full bg-emerald-500/10 px-2 py-1 text-[11px] font-semibold text-emerald-700 ring-1 ring-emerald-500/30 dark:text-emerald-200">{{ $mood }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="h-3 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                                    <div class="h-full rounded-full bg-emerald-500" style="width: {{ $progress }}%"></div>
                                </div>
                                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-600 dark:text-slate-400">
                                    <span>{{ $trip->start_date?->toFormattedDateString() }} – {{ $trip->end_date?->toFormattedDateString() }}</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-1 font-semibold text-slate-700 ring-1 ring-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-700">TZ {{ $trip->timezone ?? 'TBD' }}</span>
                                    <a href="{{ route('trips.show', $trip) }}" class="text-emerald-600 hover:text-emerald-500 dark:text-emerald-300 dark:hover:text-emerald-200">Open trip</a>
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
                @forelse($pastTrips as $trip)
                    @php $progress = $progressForTrip($trip) ?: 100; @endphp
                    <article class="rounded-3xl bg-white/90 p-5 shadow-sm ring-1 ring-slate-200 transition dark:bg-slate-900/70 dark:ring-slate-800/80" data-trip-card="{{ $trip->id }}">
                        <div class="flex gap-4">
                            <div class="relative h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-slate-100 ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">
                                <img src="{{ $trip->cover_url }}" alt="{{ $trip->title }} cover" class="h-full w-full object-cover" loading="lazy">
                                @if($trip->country_code ?? $trip->city?->country_code)
                                    <span class="absolute bottom-1 left-1 flex h-5 w-7 items-center justify-center overflow-hidden rounded-md ring-1 ring-slate-200 dark:ring-slate-700">
                                        <img src="{{ $flagUrl($trip->country_code ?? $trip->city?->country_code) }}" alt="{{ $trip->country_code ?? $trip->city?->country_code }} flag" class="h-full w-full object-cover">
                                    </span>
                                @endif
                            </div>
                            <div class="flex-1 space-y-3">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="space-y-1">
                                        <p class="text-base font-semibold text-slate-900 dark:text-slate-50 line-clamp-1">{{ $trip->title }}</p>
                                        <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-1">{{ $trip->location_label }}</p>
                                    </div>
                                    <div class="flex flex-col items-end gap-1 text-right">
                                        <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $progress }}%</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Completed</p>
                                        @if($mood = $moodForTrip($trip))
                                            <span class="rounded-full bg-emerald-500/10 px-2 py-1 text-[11px] font-semibold text-emerald-700 ring-1 ring-emerald-500/30 dark:text-emerald-200">{{ $mood }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="h-3 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                                    <div class="h-full rounded-full bg-emerald-600" style="width: {{ $progress }}%"></div>
                                </div>
                                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-600 dark:text-slate-400">
                                    <span>{{ $trip->start_date?->toFormattedDateString() }} – {{ $trip->end_date?->toFormattedDateString() }}</span>
                                    <a href="{{ route('trips.show', $trip) }}" class="text-emerald-600 hover:text-emerald-500 dark:text-emerald-300 dark:hover:text-emerald-200">View notes</a>
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
