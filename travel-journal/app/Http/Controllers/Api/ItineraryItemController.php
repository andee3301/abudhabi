<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItineraryItemRequest;
use App\Http\Resources\ItineraryItemResource;
use App\Models\ItineraryItem;
use App\Models\Trip;
use App\Support\ChecksAbilities;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ItineraryItemController extends Controller
{
    use ChecksAbilities;

    public function index(Request $request, Trip $trip)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);

        $items = $trip->itineraryItems()
            ->with(['region', 'city'])
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->orderByRaw('COALESCE(day_number, 0), sort_order, COALESCE(start_datetime, created_at)')
            ->get();

        return ItineraryItemResource::collection($items);
    }

    public function store(StoreItineraryItemRequest $request, Trip $trip)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);
        $this->ensureAbility($request, 'itinerary:write');

        $data = $request->validated();

        if (! empty($data['itinerary_id'])) {
            $belongsToTrip = $trip->itineraries()->whereKey($data['itinerary_id'])->exists();
            $data['itinerary_id'] = $belongsToTrip ? $data['itinerary_id'] : null;
        } else {
            $data['itinerary_id'] = $trip->itineraries()->where('is_primary', true)->value('id');
        }

        $data['city_id'] = $data['city_id'] ?? $trip->city_id;
        $data['country_code'] = $data['country_code'] ?? $trip->country_code;
        $data['state_region'] = $data['state_region'] ?? $trip->state_region;
        $data['city'] = $data['city'] ?? $trip->city ?? $trip->primary_location_name;
        $data['timezone'] = $data['timezone'] ?? $trip->timezone ?? 'UTC';

        if (! $data['day_number'] && ! empty($data['start_datetime']) && $trip->start_date) {
            $data['day_number'] = Carbon::parse($trip->start_date)->diffInDays(Carbon::parse($data['start_datetime'])) + 1;
        }

        $item = $trip->itineraryItems()->create($data);

        return (new ItineraryItemResource($item))
            ->response()
            ->setStatusCode(201);
    }
}
