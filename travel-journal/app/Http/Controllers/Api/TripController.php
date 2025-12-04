<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Trip::withCount(['journalEntries', 'itineraryItems'])
            ->whereBelongsTo(request()->user())
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('primary_location_name', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            })
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderByDesc('start_date');

        return TripResource::collection($query->paginate(15));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTripRequest $request)
    {
        $trip = Trip::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return (new TripResource($trip))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Trip $trip)
    {
        abort_unless($trip->user_id === request()->user()->id, 403);

        return new TripResource(
            $trip->load(['journalEntries', 'itineraryItems', 'countryVisits'])
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTripRequest $request, Trip $trip)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);

        $trip->update($request->validated());

        return new TripResource($trip);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trip $trip)
    {
        abort_unless($trip->user_id === request()->user()->id, 403);

        $trip->delete();

        return response()->noContent();
    }
}
