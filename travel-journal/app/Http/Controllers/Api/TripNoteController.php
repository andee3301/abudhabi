<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTripNoteRequest;
use App\Http\Requests\UpdateTripNoteRequest;
use App\Http\Resources\TripNoteResource;
use App\Models\Trip;
use App\Models\TripNote;
use App\Support\ChecksAbilities;
use Illuminate\Http\Request;

class TripNoteController extends Controller
{
    use ChecksAbilities;

    public function index(Request $request, Trip $trip)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);

        $notes = $trip->tripNotes()
            ->latest('note_date')
            ->latest('created_at')
            ->paginate(20);

        return TripNoteResource::collection($notes);
    }

    public function store(StoreTripNoteRequest $request, Trip $trip)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);
        $this->ensureAbility($request, 'trips:write');

        $note = TripNote::create([
            'trip_id' => $trip->id,
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'body' => $request->body,
            'note_date' => $request->note_date ?? now()->toDateString(),
            'is_pinned' => $request->boolean('is_pinned', false),
            'tags' => $request->tags ?? [],
            'metadata' => $request->metadata ?? [],
        ]);

        return (new TripNoteResource($note))
            ->response()
            ->setStatusCode(201);
    }

    public function show(TripNote $tripNote)
    {
        abort_unless($tripNote->user_id === request()->user()->id, 403);

        return new TripNoteResource($tripNote);
    }

    public function update(UpdateTripNoteRequest $request, TripNote $tripNote)
    {
        abort_unless($tripNote->user_id === $request->user()->id, 403);
        $this->ensureAbility($request, 'trips:write');

        $tripNote->update($request->validated());

        return new TripNoteResource($tripNote);
    }

    public function destroy(Request $request, TripNote $tripNote)
    {
        abort_unless($tripNote->user_id === $request->user()->id, 403);
        $this->ensureAbility($request, 'trips:write');

        $tripNote->delete();

        return response()->noContent();
    }
}
