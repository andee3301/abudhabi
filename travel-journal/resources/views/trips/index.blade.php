<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">Trips</h2>
                <p class="text-sm text-gray-600">Filter by destination, title, or status.</p>
            </div>
            <form method="GET" class="flex flex-wrap gap-2">
                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search..."
                       class="w-52 rounded-xl border border-white/50 bg-white/70 px-3 py-2 text-sm shadow-sm backdrop-blur focus:border-indigo-500 focus:ring-indigo-500">
                <select name="status" class="rounded-xl border border-white/50 bg-white/70 px-3 py-2 text-sm shadow-sm backdrop-blur focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All statuses</option>
                    <option value="planned" @selected(($filters['status'] ?? '') === 'planned')>Planned</option>
                    <option value="ongoing" @selected(($filters['status'] ?? '') === 'ongoing')>Ongoing</option>
                    <option value="completed" @selected(($filters['status'] ?? '') === 'completed')>Completed</option>
                </select>
                <button class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Filter</button>
            </form>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($trips as $trip)
                <div class="group rounded-2xl border border-white/40 bg-white/70 p-4 shadow-lg backdrop-blur transition hover:-translate-y-1 hover:shadow-xl">
                    <div class="flex items-center justify-between">
                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">{{ ucfirst($trip->status) }}</span>
                        <span class="text-xs text-gray-500">{{ $trip->journal_entries_count ?? $trip->journalEntries?->count() }} notes</span>
                    </div>
                    <h3 class="mt-2 text-lg font-semibold text-gray-900">{{ $trip->title }}</h3>
                    <p class="text-sm text-gray-600">{{ $trip->location_label }}</p>
                    <p class="text-[11px] uppercase tracking-wide text-gray-500">TZ: {{ $trip->timezone ?? 'UTC' }}</p>
                    <p class="text-xs text-gray-500">{{ $trip->start_date?->toFormattedDateString() }} – {{ $trip->end_date?->toFormattedDateString() }}</p>
                    <a href="{{ route('trips.show', $trip) }}" class="mt-3 inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                        View trip →
                    </a>
                </div>
            @empty
                <p class="text-sm text-gray-500">No trips yet.</p>
            @endforelse
        </div>

        <div>
            {{ $trips->links() }}
        </div>
    </div>
</x-app-layout>
