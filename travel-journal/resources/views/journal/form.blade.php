<x-app-layout>
    <div class="mx-auto max-w-3xl space-y-6">
        @php
            $entry = $entry ?? null;
            $isEditing = $entry !== null;
            $tripName = $trip?->title ?? $entry?->trip?->title ?? 'Select a trip on the trips page';
        @endphp
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">{{ $isEditing ? 'Edit Journal Entry' : 'New Journal Entry' }}</h2>
            <p class="text-sm text-gray-600">{{ $isEditing ? 'Update details from your journey.' : 'Capture moments from your trip.' }}</p>
        </div>

        <form method="POST" action="{{ $isEditing ? route('journal.update', $entry) : route('journal.store') }}" class="space-y-4 rounded-2xl border border-white/30 bg-white/70 p-6 shadow-lg backdrop-blur">
            @csrf
            @if($isEditing)
                @method('PUT')
            @endif
            <input type="hidden" name="trip_id" value="{{ old('trip_id', $entry?->trip_id ?? $trip?->id) }}">

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700">Title</label>
                    <input name="title" value="{{ old('title', $entry->title ?? '') }}" class="mt-1 w-full rounded-xl border border-white/50 bg-white/70 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('title') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Date</label>
                    <input type="date" name="date" value="{{ old('date', optional($entry?->entry_date ?? now())->toDateString()) }}" class="mt-1 w-full rounded-xl border border-white/50 bg-white/70 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('date') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700">Mood</label>
                    <select name="mood" class="mt-1 w-full rounded-xl border border-white/50 bg-white/70 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select</option>
                        @foreach(['joyful', 'curious', 'tired', 'reflective', 'inspired'] as $mood)
                            <option value="{{ $mood }}" @selected(old('mood', $entry?->mood ?? '') === $mood)>{{ ucfirst($mood) }}</option>
                        @endforeach
                    </select>
                    @error('mood') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Trip</label>
                    <input type="text" value="{{ $tripName }}" disabled class="mt-1 w-full rounded-xl border border-white/50 bg-white/60 px-3 py-2 text-sm text-gray-600 shadow-sm">
                    @error('trip_id') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Content</label>
                <textarea name="content" rows="6" class="mt-1 w-full rounded-xl border border-white/50 bg-white/70 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Write your story...">{{ old('content', $entry->body ?? '') }}</textarea>
                @error('content') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3">
                <button class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                    {{ $isEditing ? 'Update Entry' : 'Save Entry' }}
                </button>
                <a href="{{ $trip ? route('trips.show', $trip) : url()->previous() }}" class="text-sm font-semibold text-gray-600 hover:text-gray-800">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
