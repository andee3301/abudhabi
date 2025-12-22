<section class="space-y-4" x-data>
    <div class="flex items-center justify-between gap-3">
        <div class="space-y-1">
            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Trip timeline</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Drag to reorder; click + to add an event.</p>
        </div>
        <button type="button" @click="$dispatch('open-event-modal')" wire:click="cancelEditing"
            class="rounded-full bg-orange-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700">+ Add event</button>
    </div>

    <div class="space-y-3" data-timeline-sortable>
        @forelse($events as $event)
            <article class="surface-card flex cursor-grab items-start gap-3 p-4 ring-1 ring-slate-200/70 dark:ring-slate-800"
                data-event-id="{{ $event['id'] }}">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-500 ring-1 ring-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700">
                    @switch($event['type'])
                        @case('location') 🧭 @break
                        @case('hotel') 🏨 @break
                        @case('travel') ✈️ @break
                        @case('note') 📝 @break
                        @default •
                    @endswitch
                </div>
                <div class="flex-1 space-y-1">
                    <div class="flex items-start justify-between gap-2">
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $event['title'] }}</p>
                            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ ucfirst($event['type']) }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="startEditing({{ $event['id'] }})"
                                class="text-xs text-slate-400 hover:text-slate-600">Edit</button>
                            <button type="button" wire:click="deleteEvent({{ $event['id'] }})"
                                class="text-xs text-slate-400 hover:text-red-500">Remove</button>
                        </div>
                    </div>
                    @if($event['description'])
                        <p class="text-sm text-slate-600 dark:text-slate-300">{{ \Illuminate\Support\Str::limit($event['description'], 140) }}</p>
                    @endif
                    <div class="flex flex-wrap gap-2 text-[11px] text-slate-500 dark:text-slate-400">
                        @if($event['start_time'])
                            <span class="pill-soft">{{ optional($event['start_time'])->timezone($trip->timezone ?? 'UTC')->format('M j, H:i') }}</span>
                        @endif
                        @if($event['end_time'])
                            <span class="pill-soft">Ends {{ optional($event['end_time'])->timezone($trip->timezone ?? 'UTC')->format('M j, H:i') }}</span>
                        @endif
                        @if($event['travel_method'])
                            <span class="pill-soft">{{ ucfirst($event['travel_method']) }}</span>
                        @endif
                        @if(!empty($event['location_data']['address']))
                            <span class="pill-soft">{{ $event['location_data']['address'] }}</span>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <p class="text-sm text-slate-500 dark:text-slate-400">No events yet—add your first one.</p>
        @endforelse
    </div>

    <div x-data="eventModal()" x-on:open-event-modal.window="open()" x-on:timeline-saved.window="close()" class="relative">
        <div x-show="openState" class="fixed inset-0 z-30 bg-black/40" x-transition></div>
        <div x-show="openState" x-transition
            class="fixed inset-0 z-40 grid place-items-center p-4">
            <div class="w-full max-w-xl rounded-2xl bg-white p-5 shadow-xl ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $editingEventId ? 'Edit event' : 'Add event' }}</p>
                    <button type="button" @click="close()" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>
                <form class="mt-4 space-y-3" wire:submit.prevent="save">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="block text-sm text-slate-700 dark:text-slate-300">Type
                            <select wire:model="type"
                                class="mt-1 w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-900">
                                <option value="location">Location visit</option>
                                <option value="hotel">Hotel stay</option>
                                <option value="travel">Travel method</option>
                                <option value="note">Note</option>
                            </select>
                        </label>
                        <label class="block text-sm text-slate-700 dark:text-slate-300">Title
                            <input type="text" wire:model.defer="title"
                                class="mt-1 w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-900" />
                        </label>
                    </div>

                    <label class="block text-sm text-slate-700 dark:text-slate-300">Description
                        <textarea wire:model.defer="description"
                            class="mt-1 w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-900"
                            rows="3"></textarea>
                    </label>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="block text-sm text-slate-700 dark:text-slate-300">Start
                            <input type="datetime-local" wire:model.defer="start_time"
                                class="mt-1 w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-900" />
                        </label>
                        <label class="block text-sm text-slate-700 dark:text-slate-300">End
                            <input type="datetime-local" wire:model.defer="end_time"
                                class="mt-1 w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-900" />
                        </label>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2" x-show="isTravel()">
                        <label class="block text-sm text-slate-700 dark:text-slate-300">Travel method
                            <input type="text" wire:model.defer="travel_method"
                                class="mt-1 w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-900" />
                        </label>
                        <label class="block text-sm text-slate-700 dark:text-slate-300">From/To
                            <input type="text" wire:model.defer="location_data.address"
                                class="mt-1 w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-900" placeholder="City or address" />
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <button type="button" @click="close()" wire:click="cancelEditing"
                            class="rounded-full px-4 py-2 text-sm font-semibold text-slate-500 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">Cancel</button>
                        <button type="submit"
                            class="rounded-full bg-orange-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700">{{ $editingEventId ? 'Update' : 'Save' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js" defer></script>
    <script>
        document.addEventListener('livewire:navigated', setupSortable);
        document.addEventListener('DOMContentLoaded', setupSortable);

        function setupSortable() {
            document.querySelectorAll('[data-timeline-sortable]').forEach(function (el) {
                if (el.dataset.sortableBound) return;
                el.dataset.sortableBound = true;

                new Sortable(el, {
                    handle: '.cursor-grab',
                    animation: 120,
                    onEnd: function () {
                        const ids = Array.from(el.querySelectorAll('[data-event-id]')).map(node => node.dataset.eventId);
                        const component = Livewire.find(el.closest('[wire\:id]').getAttribute('wire:id'));
                        if (component) {
                            component.call('reorder', ids);
                        }
                    }
                });
            });
        }

        function eventModal() {
            return {
                openState: false,
                open() { this.openState = true; },
                close() { this.openState = false; },
                isTravel() {
                    return this.$wire.type === 'travel';
                }
            }
        }
    </script>
</section>
