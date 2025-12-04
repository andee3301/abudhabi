<x-app-layout>
    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">New Journal Entry</h2>
            <p class="text-sm text-gray-600">Capture moments from your trip.</p>
        </div>

        <form method="POST" action="{{ route('journal.store') }}" class="space-y-4 rounded-2xl border border-white/30 bg-white/70 p-6 shadow-lg backdrop-blur">
            @csrf
            <input type="hidden" name="trip_id" value="{{ $trip?->id }}">

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700">Title</label>
                    <input name="title" value="{{ old('title') }}" class="mt-1 w-full rounded-xl border border-white/50 bg-white/70 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('title') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Date</label>
                    <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" class="mt-1 w-full rounded-xl border border-white/50 bg-white/70 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('date') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700">Mood</label>
                    <select name="mood" class="mt-1 w-full rounded-xl border border-white/50 bg-white/70 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select</option>
                        @foreach(['joyful','curious','tired','reflective','inspired'] as $mood)
                            <option value="{{ $mood }}" @selected(old('mood') === $mood)>{{ ucfirst($mood) }}</option>
                        @endforeach
                    </select>
                    @error('mood') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Trip</label>
                    <input type="text" value="{{ $trip?->title ?? 'Select a trip on the trips page' }}" disabled class="mt-1 w-full rounded-xl border border-white/50 bg-white/60 px-3 py-2 text-sm text-gray-600 shadow-sm">
                    @error('trip_id') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Content</label>
                <textarea name="content" rows="6" class="mt-1 w-full rounded-xl border border-white/50 bg-white/70 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Write your story...">{{ old('content') }}</textarea>
                @error('content') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3">
                <button class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Save Entry</button>
                <a href="{{ url()->previous() }}" class="text-sm font-semibold text-gray-600 hover:text-gray-800">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
