@php
    $now = \Carbon\Carbon::now();
    $startOfMonth = $now->copy()->startOfMonth();
    $daysInMonth = $now->daysInMonth;
    $currentTripRange = $currentTrip ? [$currentTrip->start_date, $currentTrip->end_date] : null;
@endphp

<x-app-layout>
    <div class="space-y-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-3xl font-semibold text-gray-900">Your trip</h1>
                <p class="text-sm text-gray-600">Plan, track, and journal every journey.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('trips.index') }}"
                    class="rounded-2xl bg-white/80 px-4 py-2 text-sm font-semibold text-indigo-700 shadow-sm backdrop-blur hover:bg-white">View
                    trips</a>
                <a href="{{ route('journal.create') }}"
                    class="rounded-2xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-lg hover:bg-indigo-700">New
                    journal</a>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Left column --}}
            <div class="space-y-4">
                <div
                    class="rounded-2xl border border-white/30 bg-white/70 p-4 shadow-xl backdrop-blur-md ring-1 ring-white/50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">{{ $now->format('F Y') }}</p>
                            <h3 class="text-lg font-semibold text-gray-900">Calendar</h3>
                        </div>
                        <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">Trip
                            dates</span>
                    </div>
                    <div class="mt-4 grid grid-cols-7 gap-2 text-center text-xs text-gray-500">
                        @foreach (['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'] as $day)
                            <div>{{ $day }}</div>
                        @endforeach
                        @for ($i = 0; $i < $startOfMonth->dayOfWeek; $i++)
                            <div></div>
                        @endfor
                        @for ($d = 1; $d <= $daysInMonth; $d++)
                            @php
                                $date = $startOfMonth->copy()->addDays($d - 1);
                                $inTrip = $currentTripRange && $date->between($currentTripRange[0], $currentTripRange[1]);
                            @endphp
                            <div
                                class="rounded-lg px-2 py-2 text-sm {{ $inTrip ? 'bg-indigo-600 text-white shadow-md ring-1 ring-indigo-200' : 'bg-white/80 text-gray-700 border border-white/60' }}">
                                {{ $d }}
                            </div>
                        @endfor
                    </div>
                </div>

                <div
                    class="rounded-2xl border border-white/30 bg-white/70 p-4 shadow-xl backdrop-blur-md ring-1 ring-white/50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Recent trips</h3>
                        <span class="text-xs text-gray-500">last 3</span>
                    </div>
                    <div class="mt-4 space-y-3">
                        @forelse($recentTrips as $trip)
                            <div class="flex items-center gap-3 rounded-xl bg-white/80 p-3 shadow-sm ring-1 ring-white/60">
                                <div
                                    class="h-12 w-12 flex-shrink-0 rounded-lg bg-gradient-to-br from-indigo-400 to-sky-300 text-white grid place-items-center text-sm font-semibold">
                                    {{ strtoupper(substr($trip->primary_location_name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $trip->title }}</p>
                                    <p class="text-xs text-gray-600">{{ $trip->primary_location_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $trip->start_date?->toFormattedDateString() }} –
                                        {{ $trip->end_date?->toFormattedDateString() }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No trips yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Center column --}}
            <div class="space-y-4">
                <div
                    class="overflow-hidden rounded-2xl border border-white/30 bg-white/80 shadow-2xl backdrop-blur-lg ring-1 ring-white/50">
                    @if($currentTrip)
                        <div class="h-48 w-full bg-cover bg-center"
                            style="background-image: url('{{ $currentTrip->cover_url }}')"></div>
                        <div class="space-y-2 p-5">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-gray-500">
                                        {{ ucfirst($currentTrip->status) }}</p>
                                    <h2 class="text-2xl font-semibold text-gray-900">{{ $currentTrip->title }}</h2>
                                    <p class="text-sm text-gray-600">{{ $currentTrip->primary_location_name }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ $currentTrip->start_date?->toFormattedDateString() }} –
                                        {{ $currentTrip->end_date?->toFormattedDateString() }}</p>
                                </div>
                                @if($currentTrip->companion_name)
                                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">With
                                        {{ $currentTrip->companion_name }}</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-700">{{ \Illuminate\Support\Str::limit($currentTrip->notes, 160) }}
                            </p>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    class="rounded-full bg-white/70 px-3 py-1 text-xs font-semibold text-gray-700 shadow-sm ring-1 ring-white/50">Housing
                                    {{ $housing->count() }}</span>
                                <span
                                    class="rounded-full bg-white/70 px-3 py-1 text-xs font-semibold text-gray-700 shadow-sm ring-1 ring-white/50">Transport
                                    {{ $transport->count() }}</span>
                                <span
                                    class="rounded-full bg-white/70 px-3 py-1 text-xs font-semibold text-gray-700 shadow-sm ring-1 ring-white/50">Activities
                                    {{ $activities->count() }}</span>
                            </div>
                            <div class="pt-2">
                                <a href="{{ route('trips.show', $currentTrip) }}"
                                    class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-700">Open
                                    trip →</a>
                            </div>
                        </div>
                    @else
                        <div class="p-6 text-sm text-gray-600">No current trip. Plan your next adventure!</div>
                    @endif
                </div>

                <div
                    class="rounded-2xl border border-white/30 bg-white/80 p-5 shadow-xl backdrop-blur-md ring-1 ring-white/50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Housing</h3>
                        <span class="text-xs text-gray-500">upcoming</span>
                    </div>
                    <div class="mt-4 space-y-3">
                        @forelse($housing->take(2) as $stay)
                            <div
                                class="flex items-start justify-between rounded-xl bg-white/80 p-3 shadow-sm ring-1 ring-white/50">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $stay->title }}</p>
                                    <p class="text-xs text-gray-600">{{ $stay->location_name }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ optional($stay->start_datetime)->format('M d, H:i') }} →
                                        {{ optional($stay->end_datetime)->format('M d, H:i') }}</p>
                                </div>
                                <span
                                    class="text-xs font-semibold text-indigo-600">{{ ucfirst($stay->status ?? 'booked') }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Add a housing item to your current trip.</p>
                        @endforelse
                    </div>
                </div>

                <div
                    class="rounded-2xl border border-white/30 bg-white/80 p-5 shadow-xl backdrop-blur-md ring-1 ring-white/50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Transport</h3>
                        <a href="{{ $currentTrip ? route('trips.show', $currentTrip) : '#' }}"
                            class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">View all</a>
                    </div>
                    <div class="mt-3 space-y-3">
                        @forelse($transport->take(3) as $item)
                            <div
                                class="flex items-start justify-between rounded-xl bg-white/80 p-3 shadow-sm ring-1 ring-white/50">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $item->title }}</p>
                                    <p class="text-xs text-gray-600">{{ $item->location_name }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ optional($item->start_datetime)->format('M d, H:i') }}</p>
                                </div>
                                <span
                                    class="text-xs font-semibold text-indigo-600">{{ ucfirst($item->status ?? 'booked') }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No transport yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Right column --}}
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div
                        class="rounded-2xl border border-white/30 bg-white/80 p-4 shadow-lg backdrop-blur-md ring-1 ring-white/50">
                        <p class="text-xs text-gray-500">Trips this year</p>
                        <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $stats['tripsThisYear'] }}</p>
                        <p class="text-xs text-emerald-600">On the move</p>
                    </div>
                    <div
                        class="rounded-2xl border border-white/30 bg-white/80 p-4 shadow-lg backdrop-blur-md ring-1 ring-white/50">
                        <p class="text-xs text-gray-500">Countries visited</p>
                        <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $stats['countriesVisited'] }}</p>
                        <p class="text-xs text-indigo-600">World explorer</p>
                    </div>
                </div>

                <div
                    class="rounded-2xl border border-white/30 bg-white/80 p-5 shadow-lg backdrop-blur-md ring-1 ring-white/50">
                    <h3 class="text-lg font-semibold text-gray-900">World map</h3>
                    <div
                        class="mt-3 flex h-48 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-200 via-white to-sky-100 text-sm text-gray-600 shadow-inner">
                        Placeholder map · visited: {{ $stats['countriesVisited'] }}
                    </div>
                </div>

                <div
                    class="rounded-2xl border border-white/30 bg-white/80 p-5 shadow-lg backdrop-blur-md ring-1 ring-white/50">
                    <h3 class="text-lg font-semibold text-gray-900">Countries</h3>
                    <div class="mt-3 space-y-3">
                        @forelse($stats['countryCounts'] as $row)
                            <div
                                class="flex items-center justify-between rounded-xl bg-white/80 p-3 shadow-sm ring-1 ring-white/50">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-8 w-8 rounded-full bg-indigo-100 text-indigo-700 grid place-items-center text-xs font-semibold">
                                        {{ strtoupper($row->country_code) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ strtoupper($row->country_code) }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $row->total }} visits</p>
                                    </div>
                                </div>
                                <a href="{{ route('trips.index', ['q' => $row->country_code]) }}"
                                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">View trips</a>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No visits logged yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>