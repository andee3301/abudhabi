<x-app-layout>
    <div class="space-y-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-3xl font-semibold text-slate-900 dark:text-slate-50">Journeys</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400">Filter by destination, title, or status.</p>
            </div>
            <form method="GET" class="flex flex-wrap gap-2">
                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search..."
                       class="w-56 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-800 dark:bg-slate-900/80">
                <select name="status" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-800 dark:bg-slate-900/80">
                    <option value="">All statuses</option>
                    <option value="planned" @selected(($filters['status'] ?? '') === 'planned')>Planned</option>
                    <option value="ongoing" @selected(($filters['status'] ?? '') === 'ongoing')>Ongoing</option>
                    <option value="completed" @selected(($filters['status'] ?? '') === 'completed')>Completed</option>
                </select>
                <button class="rounded-full bg-orange-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700">Filter</button>
            </form>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($trips as $trip)
                @php $isOngoing = $trip->status === 'ongoing'; @endphp
                <div class="{{ $isOngoing ? 'sm:col-span-2 lg:col-span-3' : '' }}">
                <a href="{{ route('trips.show', $trip) }}" class="surface-card group block h-full p-5 transition hover:-translate-y-1 hover:shadow-2xl {{ $isOngoing ? 'ring-2 ring-orange-200 shadow-orange-100' : '' }}">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center rounded-full bg-orange-500/15 px-3 py-1 text-xs font-semibold text-orange-700 ring-1 ring-orange-200 dark:text-orange-200">{{ ucfirst($trip->status) }}</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400">{{ $trip->journal_entries_count ?? $trip->journalEntries?->count() }} notes</span>
                    </div>
                    <h3 class="mt-3 line-clamp-1 text-lg font-semibold text-slate-900 dark:text-slate-50">{{ $trip->title }}</h3>
                    <p class="mt-1 flex items-center gap-2 text-sm font-medium text-orange-700 dark:text-orange-300">
                        <span aria-hidden="true">📍</span>
                        <span class="line-clamp-1">{{ $trip->location_label ?: 'Location coming soon' }}</span>
                    </p>
                    <p class="text-[11px] uppercase tracking-wide text-slate-500 dark:text-slate-400">TZ: {{ $trip->timezone ?? 'UTC' }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $trip->start_date?->toFormattedDateString() }} – {{ $trip->end_date?->toFormattedDateString() }}</p>
                    <p class="mt-3 line-clamp-2 text-sm text-slate-600 dark:text-slate-400">
                        {{ $trip->notes ? \Illuminate\Support\Str::limit($trip->notes, 110) : 'Add a quick note about this journey to remember why it matters.' }}
                    </p>
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-sm font-semibold text-orange-700 dark:text-orange-200">
                        <span class="inline-flex items-center gap-2">View journey details <span class="transition duration-200 group-hover:translate-x-1">→</span></span>
                        <span class="pill-soft">Location</span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-2 text-xs font-semibold text-orange-700 ring-1 ring-orange-100 dark:bg-slate-900 dark:text-orange-200 dark:ring-slate-700">
                            Quick open
                        </span>
                    </div>
                    @if(!empty($trip->city_stops))
                        <div class="mt-3 flex flex-wrap gap-2 text-xs text-orange-700 dark:text-orange-200">
                            @foreach($trip->city_stops as $stop)
                                <span class="rounded-full bg-orange-50 px-3 py-1 font-semibold ring-1 ring-orange-100 dark:bg-slate-800 dark:ring-slate-700">📍 {{ $stop['label'] ?? 'Stop' }}</span>
                            @endforeach
                        </div>
                    @endif
                    @if($isOngoing)
                        <div class="mt-3 rounded-xl bg-orange-50 px-4 py-3 text-sm font-semibold text-orange-800 ring-1 ring-orange-100 dark:bg-slate-800 dark:text-orange-200 dark:ring-slate-700">
                            Ongoing now — tap to jump back in.
                        </div>
                    @endif
                </a>
                </div>
            @empty
                <p class="text-sm text-slate-500 dark:text-slate-400">No trips yet.</p>
            @endforelse
        </div>

        <div>
            {{ $trips->links() }}
        </div>
    </div>
</x-app-layout>
