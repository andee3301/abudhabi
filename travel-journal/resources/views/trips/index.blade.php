<x-app-layout>
    <div class="space-y-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-3xl font-semibold text-slate-900 dark:text-slate-50">Journeys</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400">Filter by destination, title, or status.</p>
            </div>
            <form method="GET" class="flex flex-wrap gap-2">
                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search..."
                       class="w-56 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-800 dark:bg-slate-900/80">
                <select name="status" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-800 dark:bg-slate-900/80">
                    <option value="">All statuses</option>
                    <option value="planned" @selected(($filters['status'] ?? '') === 'planned')>Planned</option>
                    <option value="ongoing" @selected(($filters['status'] ?? '') === 'ongoing')>Ongoing</option>
                    <option value="completed" @selected(($filters['status'] ?? '') === 'completed')>Completed</option>
                </select>
                <button class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Filter</button>
            </form>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($trips as $trip)
                <div class="surface-card group p-5 transition hover:-translate-y-1 hover:shadow-xl">
                    <div class="flex items-center justify-between">
                        <span class="pill-soft">{{ ucfirst($trip->status) }}</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400">{{ $trip->journal_entries_count ?? $trip->journalEntries?->count() }} notes</span>
                    </div>
                    <h3 class="mt-3 text-lg font-semibold text-slate-900 dark:text-slate-50 line-clamp-1">{{ $trip->title }}</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-1">{{ $trip->location_label }}</p>
                    <p class="text-[11px] uppercase tracking-wide text-slate-500 dark:text-slate-400">TZ: {{ $trip->timezone ?? 'UTC' }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $trip->start_date?->toFormattedDateString() }} – {{ $trip->end_date?->toFormattedDateString() }}</p>
                    <a href="{{ route('trips.show', $trip) }}" class="mt-3 inline-flex items-center text-sm font-semibold text-emerald-600 hover:text-emerald-500 dark:text-emerald-300 dark:hover:text-emerald-200">
                        View trip →
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
