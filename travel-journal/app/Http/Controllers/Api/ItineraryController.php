<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItineraryResource;
use App\Models\Itinerary;
use App\Models\Trip;
use App\Support\ChecksAbilities;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ItineraryController extends Controller
{
    use ChecksAbilities;

    public function index(Request $request, Trip $trip)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);
        $this->ensureAbility($request, 'itinerary:read');

        $itineraries = $trip->itineraries()
            ->with(['city', 'items' => fn ($q) => $q->orderBy('start_datetime')])
            ->orderByDesc('is_primary')
            ->get();

        return ItineraryResource::collection($itineraries);
    }

    public function store(Request $request, Trip $trip)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);
        $this->ensureAbility($request, 'itinerary:write');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_primary' => ['boolean'],
            'theme' => ['nullable', 'string', 'max:120'],
            'metadata' => ['nullable', 'array'],
        ]);

        $start = $validated['start_date'] ? Carbon::parse($validated['start_date']) : $trip->start_date;
        $end = $validated['end_date'] ? Carbon::parse($validated['end_date']) : $trip->end_date;
        $dayCount = ($start && $end) ? $start->diffInDays($end) + 1 : 0;

        $itinerary = $trip->itineraries()->create([
            ...$validated,
            'day_count' => $dayCount,
        ]);

        return (new ItineraryResource($itinerary->load('city')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Trip $trip, Itinerary $itinerary)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);
        abort_unless($itinerary->trip_id === $trip->id, 403);
        $this->ensureAbility($request, 'itinerary:write');

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'city_id' => ['sometimes', 'nullable', 'exists:cities,id'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
            'is_primary' => ['sometimes', 'boolean'],
            'theme' => ['sometimes', 'nullable', 'string', 'max:120'],
            'metadata' => ['nullable', 'array'],
        ]);

        $itinerary->update($validated);

        if (array_key_exists('start_date', $validated) || array_key_exists('end_date', $validated)) {
            $start = $validated['start_date'] ?? $itinerary->start_date;
            $end = $validated['end_date'] ?? $itinerary->end_date;
            $itinerary->update([
                'day_count' => ($start && $end) ? Carbon::parse($start)->diffInDays(Carbon::parse($end)) + 1 : $itinerary->day_count,
            ]);
        }

        return new ItineraryResource($itinerary->fresh()->load('city'));
    }
}
