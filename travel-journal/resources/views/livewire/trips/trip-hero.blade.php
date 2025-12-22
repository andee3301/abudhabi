<section
    class="rounded-3xl bg-white p-6 shadow-xl ring-1 ring-orange-100/80 dark:bg-slate-900/70 dark:ring-slate-800/80">
    <div class="grid gap-6 lg:grid-cols-[2fr,3fr] lg:items-center">
        <div
            class="relative overflow-hidden rounded-2xl bg-slate-100 ring-1 ring-orange-100/80 dark:bg-slate-800 dark:ring-slate-800">
            <div class="aspect-[4/3]">
                <img src="{{ $media['image'] ?? $trip->cover_url }}" alt="Cover image for {{ $trip->title }}"
                    class="h-full w-full object-cover" loading="lazy" decoding="async">
            </div>
            @if(($media['source'] ?? null) === 'wikimedia')
                <span
                    class="absolute bottom-3 left-3 rounded-full bg-slate-900/70 px-3 py-1 text-[11px] font-semibold text-white">Image
                    · Wikimedia</span>
            @elseif(($media['source'] ?? null) === 'unsplash')
                <span
                    class="absolute bottom-3 left-3 rounded-full bg-slate-900/70 px-3 py-1 text-[11px] font-semibold text-white">Image
                    · Unsplash</span>
            @endif
        </div>

        <div class="space-y-4">
            <div class="flex flex-wrap items-center gap-2">
                <span
                    class="rounded-full bg-orange-500/15 px-3 py-1 text-xs font-semibold text-orange-700 ring-1 ring-orange-200 dark:text-orange-200">{{ ucfirst($trip->status) }}</span>
                @if($trip->timezone)
                    <span
                        class="rounded-full bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-700">TZ
                        {{ $trip->timezone }}</span>
                @endif
                @if($trip->country_code)
                    <span
                        class="rounded-full bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-700">{{ strtoupper($trip->country_code) }}</span>
                @endif
                @php
                    $mood = collect($trip->tags ?? [])->first() ?? match ($trip->status) {
                        'ongoing' => '🧭 Adventurous',
                        'planned' => '✨ Curious',
                        'completed' => '🌧 Reflective',
                        default => '🌱 Calm',
                    };
                @endphp
                <span
                    class="rounded-full bg-orange-500/15 px-3 py-1 text-xs font-semibold text-orange-700 ring-1 ring-orange-200 dark:text-orange-200">{{ $mood }}</span>
            </div>

            <div class="space-y-2">
                <div
                    class="inline-flex items-center gap-2 rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700 ring-1 ring-orange-100 dark:bg-slate-800 dark:text-orange-200 dark:ring-slate-700">
                    <span aria-hidden="true">📍</span>
                    <span>{{ $trip->location_label ?: 'Location coming soon' }}</span>
                </div>
                <h1 class="text-3xl font-semibold text-slate-900 dark:text-slate-50">{{ $trip->title }}</h1>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    {{ $trip->start_date?->toFormattedDateString() }} –
                    {{ $trip->end_date?->toFormattedDateString() }}
                </p>
                @if($trip->companion_name)
                    <p class="text-sm text-slate-400">With {{ $trip->companion_name }}</p>
                @endif
            </div>

            <p class="text-sm leading-relaxed text-slate-700 dark:text-slate-300">
                {{ $media['description'] ?? ($trip->notes ? \Illuminate\Support\Str::limit($trip->notes, 140) : 'Add a short line to capture what this journey means to you.') }}
            </p>

            <div class="flex flex-wrap gap-2 text-xs text-slate-600 dark:text-slate-300">
                <span
                    class="rounded-full bg-slate-100 px-3 py-1 font-semibold ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">Housing
                    {{ $trip->housing()->count() }}</span>
                <span
                    class="rounded-full bg-slate-100 px-3 py-1 font-semibold ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">Transport
                    {{ $trip->transport()->count() }}</span>
                <span
                    class="rounded-full bg-slate-100 px-3 py-1 font-semibold ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">Activities
                    {{ $trip->activities()->count() }}</span>
                <span
                    class="rounded-full bg-slate-100 px-3 py-1 font-semibold ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">Notes
                    {{ $trip->journalEntries()->count() }}</span>
            </div>
        </div>
    </div>
</section>
