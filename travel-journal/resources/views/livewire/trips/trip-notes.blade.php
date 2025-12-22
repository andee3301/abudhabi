<section class="space-y-4">
    <div class="flex items-center justify-between gap-3">
        <div class="space-y-1">
            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Trip notes</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Pin highlights and keep quick thoughts with the trip.</p>
        </div>
        @if($editingNoteId)
            <button type="button" wire:click="cancelEditing" class="text-xs font-semibold text-orange-600 hover:text-orange-700">Cancel edit</button>
        @endif
    </div>

    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-orange-100/80 dark:bg-slate-900/70 dark:ring-slate-800/80">
        <form wire:submit.prevent="saveNote" class="space-y-4">
            <div class="grid gap-3 sm:grid-cols-2">
                <label class="block text-sm text-slate-700 dark:text-slate-300">Title
                    <input type="text" wire:model.defer="title"
                        class="mt-1 w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-900" />
                    @error('title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </label>
                <label class="block text-sm text-slate-700 dark:text-slate-300">Note date
                    <input type="date" wire:model.defer="note_date"
                        class="mt-1 w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-900" />
                    @error('note_date') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </label>
            </div>

            <label class="block text-sm text-slate-700 dark:text-slate-300">Body
                <textarea wire:model.defer="body" rows="3"
                    class="mt-1 w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-900"></textarea>
                @error('body') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </label>

            <div class="grid gap-3 sm:grid-cols-2">
                <label class="block text-sm text-slate-700 dark:text-slate-300">Tags
                    <input type="text" wire:model.defer="tags" placeholder="food, must-see, local tips"
                        class="mt-1 w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-900" />
                    @error('tags') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </label>
                <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                    <input type="checkbox" wire:model.defer="is_pinned"
                        class="h-4 w-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500" />
                    Pin this note
                </label>
            </div>

            <div class="flex items-center justify-between gap-2">
                <button type="submit"
                    class="rounded-full bg-orange-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700">
                    {{ $editingNoteId ? 'Update note' : 'Add note' }}
                </button>
                @if (session('status'))
                    <p class="text-xs text-emerald-600">{{ session('status') }}</p>
                @endif
            </div>
        </form>
    </div>

    <div class="space-y-3">
        @forelse($notes as $note)
            <article class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-orange-100/80 dark:bg-slate-900/70 dark:ring-slate-800/80">
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $note->title }}</p>
                            @if($note->is_pinned)
                                <span class="pill-soft">Pinned</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $note->note_date?->toFormattedDateString() ?? 'No date' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-semibold">
                        <button type="button" wire:click="startEditing({{ $note->id }})" class="text-slate-500 hover:text-slate-700">Edit</button>
                        <button type="button" wire:click="deleteNote({{ $note->id }})" class="text-rose-600 hover:text-rose-700">Delete</button>
                    </div>
                </div>
                <p class="mt-3 text-sm text-slate-700 dark:text-slate-300">{{ \Illuminate\Support\Str::limit($note->body, 220) }}</p>
                @if(!empty($note->tags))
                    <div class="mt-3 flex flex-wrap gap-2 text-xs">
                        @foreach($note->tags as $tag)
                            <span class="pill-soft">{{ $tag }}</span>
                        @endforeach
                    </div>
                @endif
            </article>
        @empty
            <p class="text-sm text-slate-500 dark:text-slate-400">No notes yet. Add the first note above.</p>
        @endforelse
    </div>
</section>
