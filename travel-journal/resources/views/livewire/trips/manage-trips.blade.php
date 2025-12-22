<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">Trips</h2>
            <p class="text-sm text-gray-500">Plan journeys and capture memories.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <div class="relative">
                <input type="search" wire:model.debounce.300ms="search" placeholder="Search trips..."
                    class="w-60 rounded-lg border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
            </div>
            <select wire:model="filterStatus"
                class="rounded-lg border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="all">All statuses</option>
                <option value="planned">Planned</option>
                <option value="ongoing">Ongoing</option>
                <option value="completed">Completed</option>
            </select>
        </div>
    </div>

    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $editingTripId ? 'Edit trip' : 'Create a trip' }}
                </h3>
                <p class="text-sm text-gray-500">
                    {{ $editingTripId ? 'Update details, status, and timing. Only one trip can be ongoing.' : 'Add a new journey. Mark it ongoing to feature it at the top.' }}
                </p>
            </div>
            @if($editingTripId)
                <button type="button" wire:click="cancelEditing"
                    class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                    Cancel
                </button>
            @endif
        </div>
        <form wire:submit.prevent="{{ $editingTripId ? 'updateTrip' : 'createTrip' }}" class="mt-4 space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700">Title</label>
                    <input wire:model.defer="title" type="text"
                        class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Summer in Lisbon" />
                    @error('title') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Primary location</label>
                    <input wire:model.defer="primary_location_name" type="text"
                        class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Lisbon, Portugal" />
                    @error('primary_location_name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">City</label>
                    <input wire:model.defer="city" type="text"
                        class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Lisbon" />
                    @error('city') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Region / State</label>
                    <select wire:model.defer="region_id"
                        class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Choose region</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}">{{ $region->name }} ({{ $region->country_code }})</option>
                        @endforeach
                    </select>
                    <input wire:model.defer="state_region" type="text"
                        class="mt-2 w-full rounded-lg border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Or type a state/region" />
                    @error('region_id') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    @error('state_region') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Country code</label>
                    <input wire:model.defer="country_code" type="text"
                        class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm uppercase focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="US" maxlength="2" />
                    @error('country_code') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Timezone</label>
                    <select wire:model.defer="timezone"
                        class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($timezones as $tz)
                            <option value="{{ $tz }}">{{ $tz }}</option>
                        @endforeach
                    </select>
                    @error('timezone') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="text-sm font-medium text-gray-700">Start date</label>
                    <input wire:model.defer="start_date" type="date"
                        class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    @error('start_date') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">End date</label>
                    <input wire:model.defer="end_date" type="date"
                        class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    @error('end_date') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Status</label>
                    <select wire:model.defer="formStatus"
                        class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="planned">Planned</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                    </select>
                    @error('formStatus') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700">Companion</label>
                    <input wire:model.defer="companion_name" type="text"
                        class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Travel buddy" />
                    @error('companion_name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700">Location overview</label>
                    <textarea wire:model.defer="location_overview" rows="2"
                        class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="What to expect about this journey's destinations."></textarea>
                    @error('location_overview') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-700">City route (multi-city)</label>
                        <textarea wire:model.defer="cityStopsInput" rows="2"
                            class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Tokyo (JP), Osaka (JP), Kyoto (JP)"></textarea>
                        <p class="mt-1 text-xs text-gray-500">Comma or newline separated. Add country code in
                            parentheses to improve matching.</p>
                        @error('cityStopsInput') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-700">Locations I want to visit</label>
                        <textarea wire:model.defer="wishlistInput" rows="2"
                            class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Mount Koya, Gion backstreets, Naoshima art sites"></textarea>
                        <p class="mt-1 text-xs text-gray-500">Comma or newline separated. Shows on the trip page.</p>
                        @error('wishlistInput') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    {{ $editingTripId ? 'Save changes' : 'Save trip' }}
                </button>
                @if (session('status'))
                    <p class="text-sm text-green-700">{{ session('status') }}</p>
                @endif
            </div>
        </form>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        @forelse($trips as $trip)
            <div
                class="relative rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 transition hover:-translate-y-0.5 hover:shadow-lg {{ $trip->status === 'ongoing' ? 'ring-2 ring-indigo-400/70 shadow-indigo-100' : '' }}">
                @if($trip->status === 'ongoing')
                    <div
                        class="absolute right-4 top-4 rounded-full bg-indigo-100 px-3 py-1 text-[11px] font-semibold text-indigo-700">
                        Now
                    </div>
                @endif
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">{{ $trip->location_label }}</p>
                        <h4 class="text-lg font-semibold text-gray-900">{{ $trip->title }}</h4>
                    </div>
                    <span
                        class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                        {{ ucfirst($trip->status) }}
                        @if($trip->status === 'ongoing')
                            <span aria-hidden="true">🧭</span>
                        @endif
                    </span>
                </div>
                <div class="mt-3 space-y-1 text-sm text-gray-600">
                    <p>{{ $trip->start_date?->toFormattedDateString() }} → {{ $trip->end_date?->toFormattedDateString() }}
                    </p>
                    <p class="text-xs text-gray-500">
                        Notes: {{ \Illuminate\Support\Str::limit($trip->notes, 80) }}
                    </p>
                </div>
                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <a href="{{ route('trips.show', $trip) }}"
                        class="inline-flex flex-1 items-center justify-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                        Open trip <span class="ml-2">→</span>
                    </a>
                    <button type="button" wire:click="startEditing({{ $trip->id }})"
                        class="inline-flex items-center rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 transition hover:border-indigo-400 hover:text-indigo-700">
                        Edit
                    </button>
                    <button type="button" wire:click="deleteTrip({{ $trip->id }})"
                        class="inline-flex items-center rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-50">
                        Delete
                    </button>
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-gray-600">
                    <button type="button" wire:click="markOngoing({{ $trip->id }})"
                        class="rounded-full border border-indigo-200 px-3 py-1 font-semibold text-indigo-700 transition hover:bg-indigo-50">
                        Set ongoing
                    </button>
                    <button type="button" wire:click="markCompleted({{ $trip->id }})"
                        class="rounded-full border border-emerald-200 px-3 py-1 font-semibold text-emerald-700 transition hover:bg-emerald-50">
                        Mark completed
                    </button>
                </div>
            </div>
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
