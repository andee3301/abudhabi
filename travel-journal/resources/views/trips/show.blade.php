@php
    $timelineDays = $trip->itineraryItems
        ->groupBy(function ($item) use ($trip) {
            $start = $item->start_datetime ? $item->start_datetime->timezone($trip->timezone ?? 'UTC') : null;

            return $start?->toDateString() ?? 'unscheduled';
        })
        ->sortBy(function ($items, $date) {
            return $date === 'unscheduled' ? '9999-12-31' : $date;
        });
@endphp
@php
    // Single mood per trip; uses first tag when available, otherwise a calm default
    $moodForTrip = function ($trip) {
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
@endphp

<x-app-layout>
    <div class="mx-auto max-w-5xl space-y-10">
        {{-- Trip hero --}}
        <section class="rounded-3xl bg-white/90 p-6 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900/70 dark:ring-slate-800/80">
            <div class="grid gap-6 lg:grid-cols-[2fr,3fr] lg:items-center">
                <div class="relative overflow-hidden rounded-2xl bg-slate-100">
                    <div class="aspect-[4/3]">
                        <img src="{{ $trip->cover_url }}" alt="Cover image for {{ $trip->title }}" class="h-full w-full object-cover">
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-700">{{ ucfirst($trip->status) }}</span>
                        @if($trip->timezone)
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-700">TZ {{ $trip->timezone }}</span>
                        @endif
                        @if($trip->country_code)
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-700">{{ strtoupper($trip->country_code) }}</span>
                        @endif
                        @if($mood = $moodForTrip($trip))
                            <span class="rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-500/30 dark:text-emerald-200">{{ $mood }}</span>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <p class="text-sm uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ $trip->location_label }}</p>
                        <h1 class="text-3xl font-semibold text-slate-900 dark:text-slate-50">{{ $trip->title }}</h1>
                        <p class="text-sm text-slate-600 dark:text-slate-400">{{ $trip->start_date?->toFormattedDateString() }} – {{ $trip->end_date?->toFormattedDateString() }}</p>
                        @if($trip->companion_name)
                            <p class="text-sm text-slate-400">With {{ $trip->companion_name }}</p>
                        @endif
                    </div>

                    <p class="text-sm leading-relaxed text-slate-700 dark:text-slate-300">
                        {{ $trip->notes ?? 'Add a short line to capture what this journey means to you.' }}
                    </p>

                    <div class="flex flex-wrap gap-2 text-xs text-slate-600 dark:text-slate-300">
                        <span class="rounded-full bg-slate-100 px-3 py-1 font-semibold ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">Housing {{ $housing->count() }}</span>
                        <span class="rounded-full bg-slate-100 px-3 py-1 font-semibold ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">Transport {{ $transport->count() }}</span>
                        <span class="rounded-full bg-slate-100 px-3 py-1 font-semibold ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">Activities {{ $activities->count() }}</span>
                        <span class="rounded-full bg-slate-100 px-3 py-1 font-semibold ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">Notes {{ $trip->journalEntries->count() }}</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- Timeline --}}
        <section class="space-y-4">
            <div class="space-y-1">
                <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Trip timeline</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Stacked days with quiet markers to show what is done.</p>
            </div>

            <div class="space-y-4">
                @forelse($timelineDays as $date => $items)
                    @php
                        $firstItem = $items->first();
                        $dateObj = $firstItem->start_datetime ? $firstItem->start_datetime->timezone($trip->timezone ?? 'UTC') : null;
                        $complete = $trip->status === 'completed' || ($dateObj && $dateObj->isPast());
                        $summary = $items->pluck('title')->filter()->take(3)->implode(' • ');
                        $location = $firstItem->city->name ?? $firstItem->city ?? $firstItem->location_name;
                    @endphp

                    <article class="rounded-2xl bg-white/90 p-5 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900/70 dark:ring-slate-800/80">
                        <div class="flex items-start gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full {{ $complete ? 'bg-emerald-500/20 text-emerald-700 ring-1 ring-emerald-500/30 dark:text-emerald-200' : 'bg-slate-100 text-slate-500 ring-1 ring-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:ring-slate-700' }}">
                                <span class="text-base font-semibold">{{ $complete ? '✓' : '•' }}</span>
                            </div>

                            <div class="flex-1 space-y-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $dateObj ? $dateObj->format('M j, Y') : 'Unscheduled' }}</p>
                                    @if($location)
                                        <span class="rounded-full bg-slate-100 px-2 py-1 text-[11px] font-semibold text-slate-700 ring-1 ring-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-700">{{ $location }}</span>
                                    @endif
                                </div>

                                <p class="text-sm text-slate-700 dark:text-slate-300">{{ $summary ?: 'Add a short note or plan for this day.' }}</p>

                                <div class="flex flex-wrap gap-2 text-xs text-slate-600 dark:text-slate-300">
                                    @foreach($items as $item)
                                        <span class="rounded-full bg-slate-100 px-2 py-1 font-semibold ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">{{ ucfirst($item->type) }} · {{ $item->title }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">No itinerary items yet. Add notes or plans to build the timeline.</p>
                @endforelse
            </div>
        </section>

        {{-- Journal entries --}}
        <section class="space-y-4">
            <div class="space-y-1">
                <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Trip notes</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Quick read of your latest reflections.</p>
            </div>

            <div class="space-y-3">
                @forelse($trip->journalEntries as $entry)
                    <article class="rounded-2xl bg-white/90 p-4 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900/70 dark:ring-slate-800/80">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $entry->title }}</p>
                            <span class="text-xs text-slate-500 dark:text-slate-400">{{ $entry->entry_date?->toFormattedDateString() }}</span>
                        </div>
                        <p class="mt-2 text-sm text-slate-700 dark:text-slate-300">{{ \Illuminate\Support\Str::limit($entry->body, 200) }}</p>
                        @if($entry->mood)
                            <p class="mt-2 text-xs text-emerald-600 dark:text-emerald-300">Mood: {{ $entry->mood }}</p>
                        @endif
                    </article>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">No journal entries yet.</p>
                @endforelse
            </div>
        </section>

        {{-- Floating action button --}}
        <a href="{{ route('journal.create', ['trip_id' => $trip->id]) }}"
           class="fixed bottom-6 right-6 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-500 text-white shadow-lg transition hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-300 focus:ring-offset-2 focus:ring-offset-slate-50 dark:focus:ring-offset-slate-950"
           aria-label="Add trip note">
            <span class="text-2xl leading-none">+</span>
        </a>
    </div>
</x-app-layout>
