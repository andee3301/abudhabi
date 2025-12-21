<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTripTimelineRequest;
use App\Http\Requests\UpdateTripTimelineRequest;
use App\Http\Resources\TripTimelineResource;
use App\Models\Trip;
use App\Models\TripTimeline;
use App\Support\ChecksAbilities;
use Illuminate\Http\Request;

class TripTimelineController extends Controller
{
    use ChecksAbilities;

    public function index(Request $request, Trip $trip)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);

        $timeline = $trip->timelineEntries()
            ->orderByDesc('occurred_at')
            ->orderByDesc('created_at')
            ->paginate(20);

        return TripTimelineResource::collection($timeline);
    }

    public function store(StoreTripTimelineRequest $request, Trip $trip)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);
        $this->ensureAbility($request, 'trips:write');

        $entry = TripTimeline::create([
            'trip_id' => $trip->id,
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'occurred_at' => $request->occurred_at,
            'type' => $request->type,
            'location_name' => $request->location_name,
            'tags' => $request->tags ?? [],
            'metadata' => $request->metadata ?? [],
        ]);

        return (new TripTimelineResource($entry))
            ->response()
            ->setStatusCode(201);
    }

    public function show(TripTimeline $tripTimeline)
    {
        abort_unless($tripTimeline->user_id === request()->user()->id, 403);

        return new TripTimelineResource($tripTimeline);
    }

    public function update(UpdateTripTimelineRequest $request, TripTimeline $tripTimeline)
    {
        abort_unless($tripTimeline->user_id === $request->user()->id, 403);
        $this->ensureAbility($request, 'trips:write');

        $tripTimeline->update($request->validated());

        return new TripTimelineResource($tripTimeline);
    }

    public function destroy(Request $request, TripTimeline $tripTimeline)
    {
        abort_unless($tripTimeline->user_id === $request->user()->id, 403);
        $this->ensureAbility($request, 'trips:write');

        $tripTimeline->delete();

        return response()->noContent();
    }
}
