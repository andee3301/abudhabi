@php
    $intel = $intel ?? [];
    $sections = [
        'summary' => 'Overview',
        'tagline' => 'Tagline',
        'local_time_label' => 'Local time',
        'currency_code' => 'Currency',
        'electrical_plugs' => 'Plugs',
        'voltage' => 'Voltage',
    ];
@endphp

<x-app-layout>
    <div class="space-y-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500">City guide</p>
                <h1 class="text-3xl font-semibold text-slate-900">{{ $city->display_name }}</h1>
                <p class="text-sm text-slate-600">{{ $intel['tagline'] ?? 'Field notes for this destination.' }}</p>
            </div>
            <a href="{{ route('dashboard') }}" class="rounded-full bg-orange-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700">
                Back to dashboard
            </a>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-orange-100/80 dark:bg-slate-900/70 dark:ring-slate-800/80">
                <p class="text-xs uppercase tracking-wide text-slate-500">Snapshot</p>
                <ul class="mt-3 space-y-2 text-sm text-slate-700 dark:text-slate-300">
                    <li><strong>Country:</strong> {{ strtoupper($city->country_code) }}</li>
                    @if($city->state_region)<li><strong>Region:</strong> {{ $city->state_region }}</li>@endif
                    @if($city->timezone)<li><strong>Timezone:</strong> {{ $city->timezone }}</li>@endif
                    @if($city->primary_language)<li><strong>Language:</strong> {{ $city->primary_language }}</li>@endif
                    @if($city->currency_code)<li><strong>Currency:</strong> {{ $city->currency_code }}</li>@endif
                    @if($city->latitude && $city->longitude)
                        <li><strong>Coords:</strong> {{ $city->latitude }}, {{ $city->longitude }}</li>
                    @endif
                </ul>
            </article>

            <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-orange-100/80 dark:bg-slate-900/70 dark:ring-slate-800/80 md:col-span-2">
                <p class="text-xs uppercase tracking-wide text-slate-500">Intel</p>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    @foreach ($sections as $key => $label)
                        @if(!empty($intel[$key]))
                            <div class="rounded-xl bg-orange-50/80 p-3 text-sm text-orange-800 ring-1 ring-orange-100 dark:bg-slate-800 dark:text-orange-200 dark:ring-slate-800">
                                <p class="text-[11px] uppercase tracking-wide text-orange-700/80 dark:text-orange-200/80">{{ $label }}</p>
                                <p class="font-semibold text-slate-900 dark:text-slate-50">{{ is_array($intel[$key]) ? implode(', ', $intel[$key]) : $intel[$key] }}</p>
                            </div>
                        @endif
                    @endforeach
                    @if(!empty($intel['weather']))
                        <div class="rounded-xl bg-orange-50/80 p-3 text-sm text-orange-800 ring-1 ring-orange-100 dark:bg-slate-800 dark:text-orange-200 dark:ring-slate-800">
                            <p class="text-[11px] uppercase tracking-wide text-orange-700/80 dark:text-orange-200/80">Weather</p>
                            <p class="font-semibold text-slate-900 dark:text-slate-50">
                                {{ $intel['weather']['label'] ?? ($intel['weather']['condition'] ?? 'Weather snapshot') }}
                            </p>
                        </div>
                    @endif
                </div>
            </article>
        </div>

        <section class="space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <p class="text-sm font-semibold text-slate-900">Trips including this city</p>
                    <p class="text-xs text-slate-500">Jump straight into journeys that mention this stop.</p>
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                @forelse($relatedTrips as $trip)
                    <a href="{{ route('trips.show', $trip) }}" class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-orange-100/80 transition hover:-translate-y-0.5 hover:shadow-lg dark:bg-slate-900/70 dark:ring-slate-800/80">
                        <p class="text-xs uppercase tracking-wide text-slate-500">{{ $trip->location_label }}</p>
                        <p class="text-lg font-semibold text-slate-900 dark:text-slate-50">{{ $trip->title }}</p>
                        <p class="text-xs text-slate-500">{{ $trip->start_date?->toFormattedDateString() }} → {{ $trip->end_date?->toFormattedDateString() }}</p>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">No journeys yet. Add this city to a trip to see it here.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
