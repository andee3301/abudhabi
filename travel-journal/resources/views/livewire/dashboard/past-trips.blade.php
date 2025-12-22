<section class="space-y-3" data-past-journeys>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="space-y-1">
            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Completed logbook</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Most recent past trips with quick completion view.</p>
        </div>
        <span class="pill-soft">{{ count($trips) }} shown</span>
    </div>

    <div class="space-y-4">
        @forelse($trips as $trip)
            <article class="surface-card relative overflow-hidden p-5 transition hover:-translate-y-0.5 hover:shadow-2xl"
                data-trip-card="{{ $trip['id'] }}">
                <div
                    class="absolute inset-x-4 top-0 h-[1px] bg-gradient-to-r from-slate-400 via-emerald-300 to-sky-400 opacity-70">
                </div>
                <div class="flex gap-4">
                    <div
                        class="relative h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-slate-100 ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">
                        <img src="{{ $trip['image'] }}" alt="{{ $trip['title'] }} cover" class="h-full w-full object-cover"
                            loading="lazy">
                        @if($trip['flag'])
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
                                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Completed</p>
                                <p class="text-lg font-semibold text-slate-900 dark:text-slate-50 line-clamp-1">
                                    {{ $trip['title'] }}
                                </p>
                                <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-1">
                                    {{ $trip['city'] ?? 'Unspecified location' }}
                                </p>
                            </div>
                            <div class="flex flex-col items-end gap-2 text-right">
                                <span class="pill-soft">{{ $trip['progress'] }}%</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3 text-xs text-slate-600 dark:text-slate-400">
                            <div
                                class="rounded-2xl bg-white/70 p-3 ring-1 ring-slate-200 dark:bg-slate-900/60 dark:ring-slate-800">
                                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Departed</p>
                                <p class="font-semibold text-slate-900 dark:text-white">
                                    {{ optional($trip['start'])->toFormattedDateString() ?? '—' }}
                                </p>
                            </div>
                            <div
                                class="rounded-2xl bg-white/70 p-3 ring-1 ring-slate-200 dark:bg-slate-900/60 dark:ring-slate-800">
                                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Returned</p>
                                <p class="font-semibold text-slate-900 dark:text-white">
                                    {{ optional($trip['end'])->toFormattedDateString() ?? '—' }}
                                </p>
                            </div>
                            <div
                                class="rounded-2xl bg-white/70 p-3 ring-1 ring-slate-200 dark:bg-slate-900/60 dark:ring-slate-800">
                                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Status</p>
                                <p class="font-semibold text-slate-900 dark:text-white">{{ ucfirst($trip['status']) }}</p>
                            </div>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                            <div class="h-full rounded-full bg-slate-500" style="width: {{ $trip['progress'] }}%"></div>
                        </div>
                        <div
                            class="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-600 dark:text-slate-300">
                            <span>{{ optional($trip['start'])->toFormattedDateString() ?? '—' }} →
                                {{ optional($trip['end'])->toFormattedDateString() ?? '—' }}</span>
                            <a href="{{ $trip['url'] }}"
                                class="inline-flex items-center rounded-full bg-orange-600 px-3 py-2 text-[11px] font-semibold text-white shadow-sm transition hover:bg-orange-700">Open
                                trip <span class="ml-1">→</span></a>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <p class="text-sm text-slate-400">Finished trips will appear here after landing.</p>
        @endforelse
    </div>

    <livewire:dashboard.past-trips-load-more :disabled="!$hasMore" />
</section>
