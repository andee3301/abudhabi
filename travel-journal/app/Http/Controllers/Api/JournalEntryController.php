<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use App\Models\JournalEntry;
use App\Models\Media;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JournalEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Trip $trip)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);

        $entries = $trip->journalEntries()
            ->with('media')
            ->latest('logged_at')
            ->paginate(20);

        return JournalEntryResource::collection($entries);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Trip $trip)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'is_public' => ['boolean'],
            'photos.*' => ['image', 'max:5120'],
        ]);

        $entry = JournalEntry::create([
            'trip_id' => $trip->id,
            'user_id' => $request->user()->id,
            'title' => $validated['title'] ?? null,
            'body' => $validated['body'],
            'location' => $validated['location'] ?? null,
            'is_public' => $validated['is_public'] ?? false,
            'logged_at' => now($trip->timezone ?? config('app.timezone')),
        ]);

        foreach ($request->file('photos', []) as $photo) {
            $path = $photo->store("journal/{$trip->id}", 'public');

            Media::create([
                'journal_entry_id' => $entry->id,
                'disk' => 'public',
                'path' => $path,
                'mime_type' => $photo->getMimeType(),
                'size' => $photo->getSize(),
            ]);
        }

        return (new JournalEntryResource($entry->load('media')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(JournalEntry $journalEntry)
    {
        abort_unless($journalEntry->user_id === request()->user()->id, 403);

        return new JournalEntryResource($journalEntry->load('media'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JournalEntry $journalEntry)
    {
        abort_unless($journalEntry->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['sometimes', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'is_public' => ['boolean'],
        ]);

        $journalEntry->update($validated);

        return new JournalEntryResource($journalEntry->fresh('media'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JournalEntry $journalEntry)
    {
        abort_unless($journalEntry->user_id === request()->user()->id, 403);

        $journalEntry->delete();

        return response()->noContent();
    }
}
