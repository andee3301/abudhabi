<x-app-layout>
    <div class="space-y-6">
        <div class="overflow-hidden rounded-2xl border border-white/30 bg-white/70 shadow-xl backdrop-blur">
            <div class="h-52 w-full bg-cover bg-center" style="background-image: url('{{ $trip->cover_url }}')"></div>
            <div class="flex flex-wrap items-center justify-between gap-3 p-6">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500">{{ $trip->location_label }}</p>
                    <h1 class="text-2xl font-semibold text-gray-900">{{ $trip->title }}</h1>
                    <p class="text-sm text-gray-600">{{ $trip->start_date?->toFormattedDateString() }} – {{ $trip->end_date?->toFormattedDateString() }}</p>
                    <p class="text-xs text-gray-500">TZ: {{ $trip->timezone ?? 'UTC' }}</p>
                    @if($trip->companion_name)
                        <p class="text-xs text-gray-500">With {{ $trip->companion_name }}</p>
                    @endif
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">{{ ucfirst($trip->status) }}</span>
                    <a href="{{ route('journal.create', ['trip_id' => $trip->id]) }}" class="rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700">Add Journal Entry</a>
                </div>
            </div>
            <div class="px-6 pb-6 text-sm text-gray-700">
                {{ $trip->notes ?? 'Add notes to this trip to keep track of ideas and plans.' }}
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <section class="space-y-4 lg:col-span-2">
                <div class="rounded-2xl border border-white/30 bg-white/70 p-5 shadow-lg backdrop-blur">
                    <h3 class="text-lg font-semibold text-gray-900">Overview</h3>
                    <p class="mt-2 text-sm text-gray-700">Key itinerary sections for this trip.</p>
                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-xl bg-white/80 p-3 shadow-sm ring-1 ring-white/50">
                            <p class="text-xs text-gray-500">Housing</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $housing->count() }}</p>
                        </div>
                        <div class="rounded-xl bg-white/80 p-3 shadow-sm ring-1 ring-white/50">
                            <p class="text-xs text-gray-500">Transport</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $transport->count() }}</p>
                        </div>
                        <div class="rounded-xl bg-white/80 p-3 shadow-sm ring-1 ring-white/50">
                            <p class="text-xs text-gray-500">Activities</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $activities->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-white/30 bg-white/70 p-5 shadow-lg backdrop-blur">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Plan itinerary</h3>
                        <span class="text-xs text-gray-500">Timezone: {{ $trip->timezone ?? 'UTC' }}</span>
                    </div>
                    <div class="mt-4">
                        @livewire('trips.plan-itinerary', ['trip' => $trip], key('planner-'.$trip->id))
                    </div>
                </div>

                <div class="rounded-2xl border border-white/30 bg-white/70 p-5 shadow-lg backdrop-blur">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Journal entries</h3>
                        <a href="{{ route('journal.create', ['trip_id' => $trip->id]) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">New entry</a>
                    </div>
                    <div class="mt-4 space-y-3">
                        @forelse($trip->journalEntries as $entry)
                            <article class="rounded-xl bg-white/80 p-3 shadow-sm ring-1 ring-white/50">
                                <div class="flex items-center justify-between text-sm text-gray-600">
                                    <span class="font-semibold text-gray-900">{{ $entry->title }}</span>
                                    <span>{{ $entry->entry_date?->toFormattedDateString() }}</span>
                                </div>
                                <p class="mt-2 text-sm text-gray-700">{{ \Illuminate\Support\Str::limit($entry->body, 180) }}</p>
                                @if($entry->mood)
                                    <p class="mt-2 text-xs text-indigo-600">Mood: {{ $entry->mood }}</p>
                                @endif
                            </article>
                        @empty
                            <p class="text-sm text-gray-500">No journal entries yet.</p>
                        @endforelse
                    </div>
                </div>
            </section>

            <aside class="space-y-4">
                <div class="rounded-2xl border border-white/30 bg-white/70 p-5 shadow-lg backdrop-blur">
                    <h3 class="text-lg font-semibold text-gray-900">Housing</h3>
                    <div class="mt-3 space-y-3">
                        @forelse($housing as $stay)
                            <div class="rounded-xl bg-white/80 p-3 shadow-sm ring-1 ring-white/50">
                                <p class="text-sm font-semibold text-gray-900">{{ $stay->title }}</p>
                                <p class="text-xs text-gray-600">{{ $stay->location_name }}</p>
                                <p class="text-xs text-gray-500">{{ optional($stay->start_datetime)->format('M d, H:i') }} – {{ optional($stay->end_datetime)->format('M d, H:i') }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No housing added.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl border border-white/30 bg-white/70 p-5 shadow-lg backdrop-blur">
                    <h3 class="text-lg font-semibold text-gray-900">Transport</h3>
                    <div class="mt-3 space-y-3">
                        @forelse($transport as $item)
                            <div class="rounded-xl bg-white/80 p-3 shadow-sm ring-1 ring-white/50">
                                <p class="text-sm font-semibold text-gray-900">{{ $item->title }}</p>
                                <p class="text-xs text-gray-600">{{ $item->location_name }}</p>
                                <p class="text-xs text-gray-500">{{ optional($item->start_datetime)->format('M d, H:i') }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No transport added.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl border border-white/30 bg-white/70 p-5 shadow-lg backdrop-blur">
                    <h3 class="text-lg font-semibold text-gray-900">Countries visited</h3>
                    <div class="mt-3 space-y-2">
                        @forelse($trip->countryVisits as $visit)
                            <div class="flex items-center justify-between rounded-xl bg-white/80 p-3 shadow-sm ring-1 ring-white/50">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ strtoupper($visit->country_code) }}</p>
                                    <p class="text-xs text-gray-600">{{ $visit->city_name }}</p>
                                </div>
                                <p class="text-xs text-gray-500">{{ $visit->visited_at?->toFormattedDateString() }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No country logs yet.</p>
                        @endforelse
                    </div>
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
