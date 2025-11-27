<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">Trips</h2>
            <p class="text-sm text-gray-500">Plan journeys, capture memories, and keep weather in sight.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <div class="relative">
                <input
                    type="search"
                    wire:model.debounce.300ms="search"
                    placeholder="Search trips..."
                    class="w-60 rounded-lg border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
            </div>
            <select
                wire:model="status"
                class="rounded-lg border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="all">All statuses</option>
                <option value="planned">Planned</option>
                <option value="in_progress">In progress</option>
                <option value="completed">Completed</option>
            </select>
        </div>
    </div>

    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
        <h3 class="text-lg font-semibold text-gray-900">Create a trip</h3>
        <form wire:submit.prevent="createTrip" class="mt-4 space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700">Title</label>
                    <input wire:model.defer="title" type="text" class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Summer in Lisbon" />
                    @error('title') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Destination</label>
                    <input wire:model.defer="destination" type="text" class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Lisbon, Portugal" />
                    @error('destination') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="text-sm font-medium text-gray-700">Start date</label>
                    <input wire:model.defer="start_date" type="date" class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    @error('start_date') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">End date</label>
                    <input wire:model.defer="end_date" type="date" class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    @error('end_date') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Status</label>
                    <select wire:model.defer="status" class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="planned">Planned</option>
                        <option value="in_progress">In progress</option>
                        <option value="completed">Completed</option>
                    </select>
                    @error('status') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700">Timezone</label>
                    <input wire:model.defer="timezone" type="text" class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Europe/Lisbon" />
                    @error('timezone') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Notes</label>
                    <textarea wire:model.defer="notes" rows="2" class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Flight, lodging, day-one ideas..."></textarea>
                    @error('notes') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Save trip
                </button>
                @if (session('status'))
                    <p class="text-sm text-green-700">{{ session('status') }}</p>
                @endif
            </div>
        </form>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        @forelse($trips as $trip)
            <a href="{{ route('trips.show', $trip) }}" class="block rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100 transition hover:shadow-md">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">{{ $trip->destination }}</p>
                        <h4 class="text-lg font-semibold text-gray-900">{{ $trip->title }}</h4>
                    </div>
                    <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                        {{ str_replace('_', ' ', ucfirst($trip->status)) }}
                    </span>
                </div>
                <div class="mt-3 text-sm text-gray-600">
                    <p>{{ $trip->start_date?->toFormattedDateString() }} → {{ $trip->end_date?->toFormattedDateString() }}</p>
                    @if($trip->latestWeather)
                        <p class="mt-1 text-xs text-gray-500">
                            Latest weather: {{ $trip->latestWeather->conditions ?? 'N/A' }} {{ $trip->latestWeather->temperature ? round($trip->latestWeather->temperature, 1).'°C' : '' }}
                        </p>
                    @endif
                </div>
            </a>
        @empty
            <div class="rounded-xl bg-white p-6 text-sm text-gray-500 ring-1 ring-gray-100">
                No trips yet. Create your first itinerary above.
            </div>
        @endforelse
    </div>

    <div>
        {{ $trips->links() }}
    </div>
</div>
