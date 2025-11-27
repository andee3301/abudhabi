<div class="space-y-6">
    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
        <h3 class="text-lg font-semibold text-gray-900">Add journal entry</h3>
        <form wire:submit.prevent="saveEntry" class="mt-4 space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700">Title</label>
                    <input wire:model.defer="entryTitle" type="text" class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Morning at the café" />
                    @error('entryTitle') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center gap-2 pt-6 md:pt-0">
                    <input wire:model.defer="is_public" id="is_public" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                    <label for="is_public" class="text-sm text-gray-700">Public entry</label>
                </div>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Body</label>
                <textarea wire:model.defer="body" rows="4" class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="What did you see, taste, or feel?"></textarea>
                @error('body') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700">Location</label>
                    <input wire:model.defer="location" type="text" class="mt-1 w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="District, venue, or coordinates" />
                    @error('location') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Photos</label>
                    <input wire:model="photos" multiple type="file" accept="image/*" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 focus:border-indigo-500 focus:ring-indigo-500" />
                    @error('photos.*') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    <div wire:loading wire:target="photos" class="mt-2 text-xs text-gray-500">Uploading...</div>
                </div>
            </div>
            @if ($photos)
                <div class="flex flex-wrap gap-3">
                    @foreach ($photos as $photo)
                        <img src="{{ $photo->temporaryUrl() }}" alt="Preview" class="h-20 w-20 rounded-lg object-cover ring-1 ring-gray-200" />
                    @endforeach
                </div>
            @endif
            <div class="flex items-center gap-3">
                <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Save entry
                </button>
                @if (session('status'))
                    <p class="text-sm text-green-700">{{ session('status') }}</p>
                @endif
            </div>
        </form>
    </div>

    <div class="space-y-4">
        @forelse ($entries as $entry)
            <article class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h4 class="text-base font-semibold text-gray-900">
                            {{ $entry->title ?? 'Journal entry' }}
                        </h4>
                        <p class="text-xs text-gray-500">
                            {{ $entry->logged_at?->toDayDateTimeString() }}
                            @if ($entry->location)
                                · {{ $entry->location }}
                            @endif
                        </p>
                    </div>
                    @if($entry->is_public)
                        <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Public</span>
                    @endif
                </div>
                <p class="mt-3 whitespace-pre-line text-sm text-gray-800">{{ $entry->body }}</p>
                @if ($entry->media->isNotEmpty())
                    <div class="mt-4 flex flex-wrap gap-3">
                        @foreach ($entry->media as $media)
                            <img src="{{ Storage::disk($media->disk)->url($media->path) }}" alt="{{ $media->caption ?? 'Photo' }}" class="h-28 w-28 rounded-lg object-cover ring-1 ring-gray-200" />
                        @endforeach
                    </div>
                @endif
            </article>
        @empty
            <div class="rounded-xl bg-white p-6 text-sm text-gray-500 ring-1 ring-gray-100">No journal entries yet. Capture your first memory above.</div>
        @endforelse
    </div>
</div>
