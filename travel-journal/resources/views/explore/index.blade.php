<x-app-layout>
    @php $intel = $intel ?? []; @endphp
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">City intelligence explorer</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400">Search a city to preview intel, prep checklists, and emergency details.</p>
            </div>
            <form method="GET" action="{{ route('explore.index') }}" class="flex gap-2">
                <input name="q" value="{{ request('q') }}" placeholder="Search location..."
                       class="w-64 rounded-xl border border-white/50 bg-white/70 px-3 py-2 text-sm shadow-sm backdrop-blur focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800/70 dark:text-slate-100">
                <button class="rounded-xl bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Search</button>
            </form>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="space-y-4 rounded-2xl border border-white/30 bg-white/70 p-5 shadow-lg backdrop-blur dark:border-slate-800 dark:bg-slate-900/70 lg:col-span-2">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-300">Intel preview</p>
                        <h3 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $city?->name ?? $destination }}</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">{{ $intel['summary'] ?? 'Pick a destination to see checklists and essentials.' }}</p>
                    </div>
                    <div class="text-right text-sm text-slate-500">
                        <p>{{ $city?->country_code }}</p>
                        <p>{{ $city?->timezone }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 text-xs">
                    <span class="pill">{{ $intel['currency_code'] ?? $city?->currency_code ?? '—' }}</span>
                    @foreach(($intel['best_months'] ?? []) as $month)
                        <span class="rounded-full bg-slate-100 px-2 py-1 text-slate-700 dark:bg-slate-700 dark:text-slate-100">{{ $month }}</span>
                    @endforeach
                    <a href="{{ route('trips.index', ['q' => $city?->name ?? $destination]) }}" class="pill-primary">Plan trip with this intel</a>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-xl bg-white/70 p-4 shadow-sm ring-1 ring-white/50 backdrop-blur dark:bg-slate-800/70 dark:ring-slate-700">
                        <p class="text-xs text-slate-500 dark:text-slate-400">Neighborhoods</p>
                        <ul class="mt-2 space-y-2 text-sm">
                            @forelse($intel['neighborhoods'] ?? [] as $hood)
                                <li class="flex items-start gap-2">
                                    <span class="mt-1 h-2 w-2 rounded-full bg-indigo-400"></span>
                                    <div>
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ $hood['name'] ?? '' }}</p>
                                        <p class="text-xs text-slate-500">{{ $hood['note'] ?? '' }}</p>
                                    </div>
                                </li>
                            @empty
                                <li class="text-sm text-slate-500">No districts added yet.</li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="rounded-xl bg-white/70 p-4 shadow-sm ring-1 ring-white/50 backdrop-blur dark:bg-slate-800/70 dark:ring-slate-700">
                        <p class="text-xs text-slate-500 dark:text-slate-400">Emergency</p>
                        <ul class="mt-2 space-y-2 text-sm">
                            @forelse($intel['emergency_numbers'] ?? [] as $contact)
                                <li class="flex items-center justify-between">
                                    <span class="font-semibold text-slate-900 dark:text-white">{{ $contact['label'] ?? $contact['service'] ?? '' }}</span>
                                    <span class="text-xs text-rose-500">{{ $contact['number'] ?? '' }}</span>
                                </li>
                            @empty
                                <li class="text-sm text-slate-500">Add emergency numbers.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="rounded-xl bg-gradient-to-br from-indigo-100 via-white to-sky-100 p-4 text-sm text-slate-700 ring-1 ring-white/60 shadow-inner dark:from-slate-800 dark:via-slate-900 dark:to-slate-800">
                    {{ $intel['weather']['snapshot'] ?? ($intel['weather']['seasonality'] ?? 'Weather snapshot placeholder') }}
                </div>
            </div>
            <div class="space-y-4">
                <div class="rounded-2xl border border-white/30 bg-white/70 p-5 shadow-lg backdrop-blur dark:border-slate-800 dark:bg-slate-900/70">
                    <h4 class="text-sm font-semibold text-slate-900 dark:text-white">Things to prepare</h4>
                    <ul class="mt-3 space-y-2 text-sm text-slate-700 dark:text-slate-200">
                        @forelse($intel['checklist'] ?? [] as $item)
                            <li class="flex items-start gap-2">
                                <span class="mt-1 h-2 w-2 rounded-full bg-indigo-500"></span>
                                <span>{{ $item }}</span>
                            </li>
                        @empty
                            <li class="text-sm text-slate-500">Add visas, SIM, and packing notes.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="rounded-2xl border border-white/30 bg-white/70 p-5 shadow-lg backdrop-blur dark:border-slate-800 dark:bg-slate-900/70">
                    <h4 class="text-sm font-semibold text-slate-900 dark:text-white">My prep notes</h4>
                    <textarea rows="6" class="mt-2 w-full rounded-xl border border-white/50 bg-white/60 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800/70 dark:text-slate-100" placeholder="Packing, visas, SIM, etc."></textarea>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-white/30 bg-white/80 p-5 shadow-xl backdrop-blur dark:border-slate-800 dark:bg-slate-900/70">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-300">City intel library</p>
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Browse sample cities</h3>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $catalog->count() }} cities loaded</p>
            </div>
            <div class="mt-4 grid gap-4 md:grid-cols-3">
                @foreach($catalog as $libraryCity)
                    <div class="rounded-2xl border border-white/30 bg-white/70 p-4 shadow-sm backdrop-blur dark:border-slate-800 dark:bg-slate-800/70">
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $libraryCity->name }}</p>
                                <p class="text-xs text-slate-500">{{ $libraryCity->state_region }} · {{ $libraryCity->country_code }}</p>
                            </div>
                            <span class="text-[11px] text-indigo-600 dark:text-indigo-300">{{ $libraryCity->timezone }}</span>
                        </div>
                        <p class="mt-2 text-xs text-slate-700 dark:text-slate-300 line-clamp-3">{{ $libraryCity->intel?->summary ?? 'Intel coming soon.' }}</p>
                        <div class="mt-3 flex flex-wrap gap-1 text-[11px]">
                            @foreach(array_slice($libraryCity->intel?->best_months ?? [], 0, 3) as $month)
                                <span class="rounded-full bg-slate-100 px-2 py-1 text-slate-700 dark:bg-slate-700 dark:text-slate-100">{{ $month }}</span>
                            @endforeach
                            @if($libraryCity->currency_code)
                                <span class="rounded-full bg-indigo-100 px-2 py-1 text-indigo-700 dark:bg-indigo-900/60 dark:text-indigo-200">{{ $libraryCity->currency_code }}</span>
                            @endif
                        </div>
                        <div class="mt-3 flex items-center justify-between text-xs">
                            <a href="{{ route('explore.index', ['q' => $libraryCity->slug]) }}" class="font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-300">Load intel →</a>
                            <span class="text-slate-500">{{ $libraryCity->intel?->visa ?? 'Visa varies' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
