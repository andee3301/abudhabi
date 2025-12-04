<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApiJournalEntryRequest;
use App\Http\Resources\JournalEntryResource;
use App\Models\JournalEntry;
use App\Models\Trip;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Trip $trip)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);

        $entries = $trip->journalEntries()
            ->latest('entry_date')
            ->paginate(20);

        return JournalEntryResource::collection($entries);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreApiJournalEntryRequest $request, Trip $trip)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);

        $entry = JournalEntry::create([
            'trip_id' => $trip->id,
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'body' => $request->body,
            'entry_date' => $request->entry_date,
            'mood' => $request->mood,
            'photo_urls' => $request->photo_urls ?? [],
        ]);

        return (new JournalEntryResource($entry))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(JournalEntry $journalEntry)
    {
        abort_unless($journalEntry->user_id === request()->user()->id, 403);

        return new JournalEntryResource($journalEntry);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JournalEntry $journalEntry)
    {
        abort_unless($journalEntry->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'body' => ['sometimes', 'string'],
            'entry_date' => ['sometimes', 'date'],
            'mood' => ['nullable', 'string', 'max:50'],
            'photo_urls' => ['nullable', 'array'],
        ]);

        $journalEntry->update($validated);

        return new JournalEntryResource($journalEntry);
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
