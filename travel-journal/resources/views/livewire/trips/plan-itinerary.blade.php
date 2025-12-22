<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div class="space-y-1">
            <p class="text-sm font-semibold text-gray-900">{{ $editingItemId ? 'Edit itinerary item' : 'Add itinerary item' }}</p>
            <p class="text-xs text-gray-500">Plan activities, travel, and notes for the trip.</p>
        </div>
        @if($editingItemId)
            <button type="button" wire:click="cancelEditing" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Cancel edit</button>
        @endif
    </div>

    <form wire:submit.prevent="{{ $editingItemId ? 'updateItem' : 'addItem' }}" class="space-y-3">
        <div class="grid gap-3 md:grid-cols-3">
            <div>
                <label class="text-xs font-semibold text-gray-600">Type</label>
                <select wire:model.defer="type" class="mt-1 w-full rounded-lg border-gray-300 px-2 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="activity">Activity</option>
                    <option value="housing">Housing</option>
                    <option value="transport">Transport</option>
                    <option value="note">Note</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="text-xs font-semibold text-gray-600">Title</label>
                <input wire:model.defer="title" type="text" class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Sunset ferry to Lisbon" />
                @error('title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="grid gap-3 md:grid-cols-3">
            <div>
                <label class="text-xs font-semibold text-gray-600">Start</label>
                <input wire:model.defer="start_datetime" type="datetime-local" class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                @error('start_datetime') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">End</label>
                <input wire:model.defer="end_datetime" type="datetime-local" class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                @error('end_datetime') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Day</label>
                <input wire:model.defer="day_number" type="number" min="1" class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="1" />
                @error('day_number') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="grid gap-3 md:grid-cols-3">
            <div>
                <label class="text-xs font-semibold text-gray-600">City</label>
                <input wire:model.defer="city" type="text" class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Lisbon" />
                @error('city') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Country</label>
                <input wire:model.defer="country_code" type="text" maxlength="2" class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 text-sm uppercase shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="PT" />
                @error('country_code') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Timezone</label>
                <select wire:model.defer="timezone" class="mt-1 w-full rounded-lg border-gray-300 px-2 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach($timezones as $tz)
                        <option value="{{ $tz }}">{{ $tz }}</option>
                    @endforeach
                </select>
                @error('timezone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="flex items-center justify-between text-xs text-gray-500">
            <button type="button" wire:click="prefillFromTrip" class="text-indigo-600 hover:text-indigo-700">Use trip defaults</button>
            <span class="text-gray-500">Auto-fills from active city intel.</span>
        </div>
        <div class="grid gap-3 md:grid-cols-2">
            <div>
                <label class="text-xs font-semibold text-gray-600">Location name</label>
                <input wire:model.defer="location_name" type="text" class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Cais do Sodré ferry terminal" />
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Status</label>
                <select wire:model.defer="status" class="mt-1 w-full rounded-lg border-gray-300 px-2 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="planned">Planned</option>
                    <option value="tentative">Tentative</option>
                    <option value="booked">Booked</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                {{ $editingItemId ? 'Update item' : 'Add item' }}
            </button>
            <p class="text-xs text-gray-500">Updates timeline instantly.</p>
        </div>
    </form>

    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <h4 class="text-sm font-semibold text-gray-900">Upcoming</h4>
            <span class="text-xs text-gray-500">{{ $upcoming->count() }} items</span>
        </div>
        @forelse($upcoming as $item)
            <div class="flex items-start justify-between gap-3 rounded-lg bg-white/80 p-3 shadow-sm ring-1 ring-white/60">
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ $item->title }}</p>
                    <p class="text-xs text-gray-600">{{ ucfirst($item->type) }} · {{ $item->city ?? $item->location_name }}</p>
                    <p class="text-[11px] text-gray-500">{{ optional($item->start_datetime)->format('M d, H:i') }} {{ $item->timezone ?? $trip->timezone ?? 'UTC' }}</p>
                </div>
                <div class="flex flex-col items-end gap-2 text-[11px] font-semibold text-indigo-600">
                    <span>{{ ucfirst($item->status ?? 'planned') }}</span>
                    <div class="flex items-center gap-2 text-[11px] font-semibold">
                        <button type="button" wire:click="startEditing({{ $item->id }})" class="text-indigo-600 hover:text-indigo-700">Edit</button>
                        <button type="button" wire:click="deleteItem({{ $item->id }})" class="text-rose-600 hover:text-rose-700">Delete</button>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-500">No itinerary yet. Add your first stop above.</p>
        @endforelse
    </div>
</div>
