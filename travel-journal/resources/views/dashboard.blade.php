@php
    $primaryActive = collect($activeJourneys ?? [])->first();
    $pastCollection = collect($pastJourneys ?? []);
    $pastPreview = $pastCollection->take(3);
    $pastOverflow = $pastCollection->skip(3);
@endphp

<x-app-layout>
    <div class="flex-1 space-y-10">
        <section
            class="relative overflow-hidden rounded-[28px] border border-slate-200/70 bg-gradient-to-br from-sky-50 via-white to-emerald-50 p-6 shadow-xl ring-1 ring-white/40 dark:border-slate-800 dark:bg-gradient-to-br dark:from-slate-950 dark:via-slate-900 dark:to-sky-950 dark:ring-slate-800">
            <div
                class="pointer-events-none absolute inset-0 opacity-70 [background-image:radial-gradient(circle_at_20%_18%,rgba(14,165,233,.12),transparent_36%),radial-gradient(circle_at_86%_10%,rgba(52,211,153,.14),transparent_34%),linear-gradient(120deg,rgba(14,165,233,.08),rgba(255,255,255,0),rgba(16,185,129,.08))]">
            </div>
            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-3">
                    <p class="text-[11px] uppercase tracking-[0.32em] text-slate-500">Flight deck</p>
                    <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Journey board</h1>
                    <p class="max-w-3xl text-sm text-slate-600 dark:text-slate-300">Aviation-flavored overview that
                        keeps your routes, layovers, and memories in one place.</p>
                    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-700 dark:text-slate-200">
                        <span class="pill-soft">Journeys: {{ $stats['totalTrips'] ?? 0 }}</span>
                        <span class="pill-soft">This year: {{ $stats['tripsThisYear'] ?? 0 }}</span>
                        <span class="pill-soft">Countries: {{ $stats['countriesVisited'] ?? 0 }}</span>
                    </div>
                </div>
                @if($primaryActive)
                    @php $primaryIsOngoing = ($primaryActive['status'] ?? null) === 'ongoing'; @endphp
                    <div
                        class="surface-card relative w-full max-w-xl overflow-hidden border border-slate-200/80 bg-white/90 p-5 shadow-xl ring-1 ring-white/40 dark:border-slate-800/70 dark:bg-slate-900/80 dark:ring-slate-800 {{ $primaryIsOngoing ? 'ring-2 ring-orange-200 shadow-orange-100' : '' }}">
                        <div
                            class="absolute inset-x-5 top-0 h-[1px] bg-gradient-to-r from-emerald-400 via-sky-300 to-indigo-400 opacity-70">
                        </div>
                        <div class="flex items-start justify-between gap-3">
                            <div class="space-y-2">
                                <p class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Primary route</p>
                                <p class="text-xl font-semibold text-slate-900 dark:text-white">
                                    {{ $primaryActive['title'] }}</p>
                                <p class="text-sm text-slate-500 dark:text-slate-300">
                                    {{ $primaryActive['city'] ?? 'Unspecified location' }}</p>
                            </div>
                            <div class="text-right text-xs text-slate-600 dark:text-slate-300">
                                <p class="pill-accent inline-flex">{{ ucfirst($primaryActive['status'] ?? 'Active') }}</p>
                                <p class="mt-1 text-[11px] uppercase tracking-[0.12em]">TZ
                                    {{ $primaryActive['timezone'] ?? 'TBD' }}</p>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-3 gap-3 text-xs text-slate-700 dark:text-slate-200">
                            <div
                                class="rounded-2xl bg-white/70 p-3 ring-1 ring-slate-200 dark:bg-slate-900/70 dark:ring-slate-800">
                                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Depart</p>
                                <p class="font-semibold">
                                    {{ optional($primaryActive['start'])->toFormattedDateString() ?? '—' }}</p>
                            </div>
                            <div
                                class="rounded-2xl bg-white/70 p-3 ring-1 ring-slate-200 dark:bg-slate-900/70 dark:ring-slate-800">
                                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Arrive</p>
                                <p class="font-semibold">
                                    {{ optional($primaryActive['end'])->toFormattedDateString() ?? '—' }}</p>
                            </div>
                            <div
                                class="rounded-2xl bg-white/70 p-3 ring-1 ring-slate-200 dark:bg-slate-900/70 dark:ring-slate-800">
                                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Mood</p>
                                <p class="font-semibold">{{ $primaryActive['mood'] ?? '✈︎ Ready' }}</p>
                            </div>
                        </div>
                        <div class="mt-4 h-2 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                            <div class="h-full rounded-full bg-emerald-500"
                                style="width: {{ $primaryActive['progress'] }}%"></div>
                        </div>
                        <div class="mt-3 flex items-center justify-between text-xs text-slate-600 dark:text-slate-300">
                            <span>{{ optional($primaryActive['start'])->toFormattedDateString() ?? '—' }} →
                                {{ optional($primaryActive['end'])->toFormattedDateString() ?? '—' }}</span>
                            <a href="{{ $primaryActive['url'] }}"
                                class="inline-flex items-center rounded-full bg-orange-600 px-3 py-2 text-[11px] font-semibold text-white shadow-sm transition hover:bg-orange-700">
                                Open trip
                                <span class="ml-2">→</span>
                            </a>
                        </div>
                        @if(!empty($primaryActive['city_stops']))
                            <div class="mt-3 flex flex-wrap gap-2 text-[11px] text-slate-600 dark:text-slate-300">
                                @foreach($primaryActive['city_stops'] as $stop)
                                    <span
                                        class="rounded-full bg-white px-3 py-1 font-semibold ring-1 ring-orange-100 dark:bg-slate-900 dark:ring-slate-800">
                                        📍 {{ $stop['label'] ?? 'Stop' }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-5">
            <div class="space-y-6 xl:col-span-3">
                <section class="space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Current manifest</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Everything in motion or on deck.</p>
                        </div>
                        <span class="pill-soft">{{ $activeJourneys->count() }} in play</span>
                    </div>

                    <div class="space-y-4">
                        @forelse($activeJourneys as $trip)
                            <article
                                class="surface-card relative overflow-hidden p-5 transition hover:-translate-y-0.5 hover:shadow-2xl"
                                data-trip-card="{{ $trip['id'] }}">
                                <div
                                    class="absolute inset-x-4 top-0 h-[1px] bg-gradient-to-r from-emerald-400 via-sky-300 to-indigo-400 opacity-70">
                                </div>
                                <div class="flex gap-4">
                                    <div
                                        class="relative h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-slate-100 ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">
                                        <img src="{{ $trip['image'] }}" alt="{{ $trip['title'] }} cover"
                                            class="h-full w-full object-cover" loading="lazy">
                                        @if($trip['country_code'])
                                            <span
                                                class="absolute bottom-1 left-1 flex h-5 w-7 items-center justify-center overflow-hidden rounded-md ring-1 ring-slate-200 dark:ring-slate-700">
                                                <img src="{{ $trip['flag'] }}" alt="{{ $trip['country_code'] }} flag"
                                                    class="h-full w-full object-cover" loading="lazy" decoding="async">
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex-1 space-y-3">
                                        <div class="flex flex-wrap items-start justify-between gap-3">
                                            <div class="space-y-1">
                                                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">In flight
                                                </p>
                                                <p
                                                    class="text-lg font-semibold text-slate-900 dark:text-slate-50 line-clamp-1">
                                                    {{ $trip['title'] }}</p>
                                                <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-1">
                                                    {{ $trip['city'] ?? 'Unspecified location' }}</p>
                                            </div>
                                            <div class="flex flex-col items-end gap-2 text-right">
                                                <span class="pill-soft">{{ ucfirst($trip['status']) }}</span>
                                                @if($trip['mood'])
                                                    <span class="pill-accent">{{ $trip['mood'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-3 gap-3 text-xs text-slate-600 dark:text-slate-400">
                                            <div
                                                class="rounded-2xl bg-white/70 p-3 ring-1 ring-slate-200 dark:bg-slate-900/60 dark:ring-slate-800">
                                                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Depart</p>
                                                <p class="font-semibold text-slate-900 dark:text-white">
                                                    {{ optional($trip['start'])->toFormattedDateString() ?? '—' }}</p>
                                            </div>
                                            <div
                                                class="rounded-2xl bg-white/70 p-3 ring-1 ring-slate-200 dark:bg-slate-900/60 dark:ring-slate-800">
                                                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Arrive</p>
                                                <p class="font-semibold text-slate-900 dark:text-white">
                                                    {{ optional($trip['end'])->toFormattedDateString() ?? '—' }}</p>
                                            </div>
                                            <div
                                                class="rounded-2xl bg-white/70 p-3 ring-1 ring-slate-200 dark:bg-slate-900/60 dark:ring-slate-800">
                                                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Timezone
                                                </p>
                                                <p class="font-semibold text-slate-900 dark:text-white">
                                                    {{ $trip['timezone'] ?? 'TBD' }}</p>
                                            </div>
                                        </div>
                                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                                            <div class="h-full rounded-full bg-emerald-500"
                                                style="width: {{ $trip['progress'] }}%"></div>
                                        </div>
                                        @if(!empty($trip['city_stops']))
                                            <div
                                                class="mt-2 flex flex-wrap gap-2 text-[11px] text-slate-600 dark:text-slate-300">
                                                @foreach($trip['city_stops'] as $stop)
                                                    <span
                                                        class="rounded-full bg-white px-3 py-1 font-semibold ring-1 ring-orange-100 dark:bg-slate-900 dark:ring-slate-800">
                                                        📍 {{ $stop['label'] ?? 'Stop' }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                        <div
                                            class="mt-2 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-600 dark:text-slate-300">
                                            <span>{{ optional($trip['start'])->toFormattedDateString() ?? '—' }} →
                                                {{ optional($trip['end'])->toFormattedDateString() ?? '—' }}</span>
                                            <a href="{{ $trip['url'] }}"
                                                class="inline-flex items-center rounded-full bg-orange-600 px-3 py-2 text-[11px] font-semibold text-white shadow-sm transition hover:bg-orange-700">
                                                Open trip <span class="ml-1">→</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <p class="text-sm text-slate-400">No journeys in motion. Plot the next takeoff.</p>
                        @endforelse
                    </div>
                </section>

                <section class="space-y-3" data-past-journeys>
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Completed logbook</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Three most recent by default. Expand
                                to relive more.</p>
                        </div>
                        <span class="pill-soft">{{ $pastCollection->count() }} recorded</span>
                    </div>

                    <div class="space-y-4">
                        @forelse($pastPreview as $trip)
                            <article
                                class="surface-card relative overflow-hidden p-5 transition hover:-translate-y-0.5 hover:shadow-2xl"
                                data-trip-card="{{ $trip['id'] }}">
                                <div
                                    class="absolute inset-x-4 top-0 h-[1px] bg-gradient-to-r from-slate-400 via-emerald-300 to-sky-400 opacity-70">
                                </div>
                                <div class="flex gap-4">
                                    <div
                                        class="relative h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-slate-100 ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">
                                        <img src="{{ $trip['image'] }}" alt="{{ $trip['title'] }} cover"
                                            class="h-full w-full object-cover" loading="lazy">
                                        @if($trip['country_code'])
                                            <span
                                                class="absolute bottom-1 left-1 flex h-5 w-7 items-center justify-center overflow-hidden rounded-md ring-1 ring-slate-200 dark:ring-slate-700">
                                                <img src="{{ $trip['flag'] }}" alt="{{ $trip['country_code'] }} flag"
                                                    class="h-full w-full object-cover" loading="lazy" decoding="async">
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex-1 space-y-3">
                                        <div class="flex flex-wrap items-start justify-between gap-3">
                                            <div class="space-y-1">
                                                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Completed
                                                </p>
                                                <p
                                                    class="text-lg font-semibold text-slate-900 dark:text-slate-50 line-clamp-1">
                                                    {{ $trip['title'] }}</p>
                                                <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-1">
                                                    {{ $trip['city'] ?? 'Unspecified location' }}</p>
                                            </div>
                                            <div class="flex flex-col items-end gap-2 text-right">
                                                <span class="pill-soft">100%</span>
                                                @if($trip['mood'])
                                                    <span class="pill-accent">{{ $trip['mood'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-3 gap-3 text-xs text-slate-600 dark:text-slate-400">
                                            <div
                                                class="rounded-2xl bg-white/70 p-3 ring-1 ring-slate-200 dark:bg-slate-900/60 dark:ring-slate-800">
                                                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Departed
                                                </p>
                                                <p class="font-semibold text-slate-900 dark:text-white">
                                                    {{ optional($trip['start'])->toFormattedDateString() ?? '—' }}</p>
                                            </div>
                                            <div
                                                class="rounded-2xl bg-white/70 p-3 ring-1 ring-slate-200 dark:bg-slate-900/60 dark:ring-slate-800">
                                                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Returned
                                                </p>
                                                <p class="font-semibold text-slate-900 dark:text-white">
                                                    {{ optional($trip['end'])->toFormattedDateString() ?? '—' }}</p>
                                            </div>
                                            <div
                                                class="rounded-2xl bg-white/70 p-3 ring-1 ring-slate-200 dark:bg-slate-900/60 dark:ring-slate-800">
                                                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Status</p>
                                                <p class="font-semibold text-slate-900 dark:text-white">
                                                    {{ ucfirst($trip['status']) }}</p>
                                            </div>
                                        </div>
                                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                                            <div class="h-full rounded-full bg-slate-500" style="width: 100%"></div>
                                        </div>
                                        <div
                                            class="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-600 dark:text-slate-300">
                                            <span>{{ optional($trip['start'])->toFormattedDateString() ?? '—' }} →
                                                {{ optional($trip['end'])->toFormattedDateString() ?? '—' }}</span>
                                            <a href="{{ $trip['url'] }}"
                                                class="text-emerald-600 hover:text-emerald-500 dark:text-emerald-300 dark:hover:text-emerald-200">View
                                                trip</a>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <p class="text-sm text-slate-400">Finished trips will appear here after landing.</p>
                        @endforelse

                        @foreach($pastOverflow as $trip)
                            <article
                                class="surface-card relative hidden overflow-hidden p-5 transition hover:-translate-y-0.5 hover:shadow-2xl"
                                data-past-extra data-trip-card="{{ $trip['id'] }}">
                                <div
                                    class="absolute inset-x-4 top-0 h-[1px] bg-gradient-to-r from-slate-400 via-emerald-300 to-sky-400 opacity-70">
                                </div>
                                <div class="flex gap-4">
                                    <div
                                        class="relative h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-slate-100 ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">
                                        <img src="{{ $trip['image'] }}" alt="{{ $trip['title'] }} cover"
                                            class="h-full w-full object-cover" loading="lazy">
                                        @if($trip['country_code'])
                                            <span
                                                class="absolute bottom-1 left-1 flex h-5 w-7 items-center justify-center overflow-hidden rounded-md ring-1 ring-slate-200 dark:ring-slate-700">
                                                <img src="{{ $trip['flag'] }}" alt="{{ $trip['country_code'] }} flag"
                                                    class="h-full w-full object-cover" loading="lazy" decoding="async">
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex-1 space-y-3">
                                        <div class="flex flex-wrap items-start justify-between gap-3">
                                            <div class="space-y-1">
                                                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Completed
                                                </p>
                                                <p
                                                    class="text-lg font-semibold text-slate-900 dark:text-slate-50 line-clamp-1">
                                                    {{ $trip['title'] }}</p>
                                                <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-1">
                                                    {{ $trip['city'] ?? 'Unspecified location' }}</p>
                                            </div>
                                            <div class="flex flex-col items-end gap-2 text-right">
                                                <span class="pill-soft">100%</span>
                                                @if($trip['mood'])
                                                    <span class="pill-accent">{{ $trip['mood'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-3 gap-3 text-xs text-slate-600 dark:text-slate-400">
                                            <div
                                                class="rounded-2xl bg-white/70 p-3 ring-1 ring-slate-200 dark:bg-slate-900/60 dark:ring-slate-800">
                                                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Departed
                                                </p>
                                                <p class="font-semibold text-slate-900 dark:text-white">
                                                    {{ optional($trip['start'])->toFormattedDateString() ?? '—' }}</p>
                                            </div>
                                            <div
                                                class="rounded-2xl bg-white/70 p-3 ring-1 ring-slate-200 dark:bg-slate-900/60 dark:ring-slate-800">
                                                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Returned
                                                </p>
                                                <p class="font-semibold text-slate-900 dark:text-white">
                                                    {{ optional($trip['end'])->toFormattedDateString() ?? '—' }}</p>
                                            </div>
                                            <div
                                                class="rounded-2xl bg-white/70 p-3 ring-1 ring-slate-200 dark:bg-slate-900/60 dark:ring-slate-800">
                                                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Status</p>
                                                <p class="font-semibold text-slate-900 dark:text-white">
                                                    {{ ucfirst($trip['status']) }}</p>
                                            </div>
                                        </div>
                                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                                            <div class="h-full rounded-full bg-slate-500" style="width: 100%"></div>
                                        </div>
                                        <div
                                            class="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-600 dark:text-slate-300">
                                            <span>{{ optional($trip['start'])->toFormattedDateString() ?? '—' }} →
                                                {{ optional($trip['end'])->toFormattedDateString() ?? '—' }}</span>
                                            <a href="{{ $trip['url'] }}"
                                                class="text-emerald-600 hover:text-emerald-500 dark:text-emerald-300 dark:hover:text-emerald-200">View
                                                trip</a>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    @if($pastOverflow->isNotEmpty())
                        <div class="pt-1">
                            <button type="button" data-past-toggle data-collapsed-label="Show more journeys"
                                data-expanded-label="Hide history"
                                class="w-full rounded-full bg-slate-900 px-4 py-3 text-sm font-semibold text-slate-100 shadow-lg transition hover:bg-slate-800 dark:bg-emerald-500 dark:text-slate-950 dark:hover:bg-emerald-400">
                                Show more journeys
                            </button>
                        </div>
                    @endif
                </section>
            </div>

            <div class="space-y-6 xl:col-span-2">
                <section class="surface-card p-5 ring-1 ring-slate-200/70 dark:ring-slate-800">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Global radar</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">OpenStreetMap with theme-aware tiles ·
                                {{ $mapPoints->count() }} pins</p>
                        </div>
                        <div
                            class="flex items-center gap-2 text-[11px] uppercase tracking-[0.12em] text-slate-500 dark:text-slate-400">
                            <span class="pill-soft">Light</span>
                            <span class="pill-soft">Dark</span>
                        </div>
                    </div>
                    <div class="mt-4 h-[420px] overflow-hidden rounded-2xl border border-slate-200/70 bg-slate-50 shadow-inner ring-1 ring-slate-200/60 dark:border-slate-800 dark:bg-slate-950 dark:ring-slate-800"
                        data-journey-map data-map-points='@json($mapPoints)'>
                        <div class="grid h-full place-items-center text-xs text-slate-500 dark:text-slate-400">Loading
                            flight map…</div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
