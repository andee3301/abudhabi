<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">{{ $trip->destination }}</p>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ $trip->title }}
                </h2>
                <p class="text-sm text-gray-500">{{ $trip->start_date?->toFormattedDateString() }} → {{ $trip->end_date?->toFormattedDateString() }}</p>
            </div>
            <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                {{ ucfirst(str_replace('_', ' ', $trip->status)) }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Trip overview</h3>
                        @if($trip->notes)
                            <p class="mt-2 text-sm text-gray-700">{{ $trip->notes }}</p>
                        @endif
                    </div>
                    @if($trip->latestWeather)
                        <div class="text-right text-sm text-gray-600">
                            <p class="font-semibold">Latest weather</p>
                            <p>{{ $trip->latestWeather->conditions ?? 'N/A' }} {{ $trip->latestWeather->temperature ? round($trip->latestWeather->temperature, 1).'°C' : '' }}</p>
                            <p class="text-xs text-gray-500">{{ $trip->latestWeather->recorded_at?->diffForHumans() }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <livewire:trips.journal-timeline :trip="$trip" />

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Weather log</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Recorded</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Conditions</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Temp (°C)</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Humidity</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Provider</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($trip->weatherSnapshots as $weather)
                                <tr>
                                    <td class="px-3 py-2 text-gray-700">{{ $weather->recorded_at?->toDayDateTimeString() }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $weather->conditions ?? '—' }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $weather->temperature !== null ? round($weather->temperature, 1) : '—' }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $weather->humidity ?? '—' }}%</td>
                                    <td class="px-3 py-2 text-gray-700">{{ ucfirst($weather->provider) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-4 text-center text-gray-500">No weather snapshots yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
