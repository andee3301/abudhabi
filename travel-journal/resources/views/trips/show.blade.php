<x-app-layout>
    <div class="mx-auto max-w-5xl space-y-10">
        <livewire:trips.trip-hero :trip="$trip" />

        <section class="grid gap-4 lg:grid-cols-2">
            <article
                class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-orange-100/80 dark:bg-slate-900/70 dark:ring-slate-800/80">
                @php
                    $countryCode = $trip->city?->country_code ?? $trip->country_code;
                    $regionName = $trip->region?->name ?? $trip->state_region;
                    $durationDays = ($trip->start_date && $trip->end_date)
                        ? $trip->start_date->diffInDays($trip->end_date) + 1
                        : null;
                    $localTime = $trip->timezone ? now()->setTimezone($trip->timezone)->format('H:i') : null;
                @endphp
                <div class="flex items-center justify-between gap-2">
                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Journey location</p>
                    <span class="pill-accent">On the map</span>
                </div>
                <dl class="mt-3 space-y-3 text-sm text-slate-700 dark:text-slate-300">
                    <div class="flex items-center gap-3">
                        <span class="text-lg">🗺️</span>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Location</dt>
                            <dd class="font-semibold text-slate-900 dark:text-slate-50">
                                {{ $trip->location_label ?: 'Location coming soon' }}
                            </dd>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-lg">🌍</span>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Region</dt>
                            <dd>{{ $regionName ?: '—' }} @if($countryCode) · {{ strtoupper($countryCode) }} @endif</dd>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-lg">⏱</span>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Timezone</dt>
                            <dd>{{ $trip->timezone ?? 'UTC' }} @if($localTime) · Local {{ $localTime }} @endif</dd>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-lg">📅</span>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Dates</dt>
                            <dd>{{ $trip->start_date?->toFormattedDateString() }} –
                                {{ $trip->end_date?->toFormattedDateString() }}
                                @if($durationDays) · {{ $durationDays }} days @endif
                            </dd>
                        </div>
                    </div>
                </dl>
                @if($trip->location_overview)
                    <div
                        class="mt-4 rounded-xl bg-orange-50 px-4 py-3 text-sm text-orange-800 ring-1 ring-orange-100 dark:bg-slate-800 dark:text-orange-200 dark:ring-slate-800">
                        {{ $trip->location_overview }}
                    </div>
                @endif
                @if($cityStops->isNotEmpty())
                    <div class="mt-4 space-y-2">
                        <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Route</p>
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach($cityStops as $stop)
                                <a href="{{ $stop['city'] ? route('cities.guide', $stop['city']) : '#' }}"
                                    class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-2 text-xs font-semibold text-orange-700 ring-1 ring-orange-100 transition hover:bg-orange-50 dark:bg-slate-900 dark:text-orange-200 dark:ring-slate-700">
                                    <span aria-hidden="true">📍</span>
                                    {{ $stop['label'] }}
                                    @if($stop['country_code']) <span
                                    class="text-[10px] text-slate-500">{{ strtoupper($stop['country_code']) }}</span> @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </article>

            <article
                class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-orange-100/80 dark:bg-slate-900/70 dark:ring-slate-800/80">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Notes about this place</p>
                    <span class="pill-soft">{{ $trip->journalEntries->count() }} journal
                        {{ \Illuminate\Support\Str::plural('note', $trip->journalEntries->count()) }}</span>
                </div>
                <p class="mt-3 text-sm leading-relaxed text-slate-700 dark:text-slate-300">
                    {{ $trip->notes ?? 'Keep a quick description here so you can remember what drew you to this spot.' }}
                </p>
                @if($trip->journalEntries->first()?->mood)
                    <p class="mt-3 text-xs text-orange-700 dark:text-orange-200">Latest mood captured:
                        {{ $trip->journalEntries->first()->mood }}
                    </p>
                @endif
                @if(!empty($wishlist))
                    <div class="mt-4 space-y-2">
                        <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Locations I want to
                            visit</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($wishlist as $wish)
                                <span
                                    class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-orange-700 ring-1 ring-orange-100 dark:bg-slate-800 dark:text-orange-200 dark:ring-slate-700">{{ $wish }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </article>
        </section>

        <livewire:trips.trip-timeline-board :trip="$trip" />
    </div>
</x-app-layout>
