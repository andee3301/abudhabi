<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItineraryItemRequest;
use App\Http\Resources\ItineraryItemResource;
use App\Models\ItineraryItem;
use App\Models\Trip;
use Illuminate\Http\Request;

class ItineraryItemController extends Controller
{
    public function index(Request $request, Trip $trip)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);

        $items = $trip->itineraryItems()
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->orderBy('start_datetime')
            ->get();

        return ItineraryItemResource::collection($items);
    }

    public function store(StoreItineraryItemRequest $request, Trip $trip)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);

        $item = $trip->itineraryItems()->create($request->validated());

        return (new ItineraryItemResource($item))
            ->response()
            ->setStatusCode(201);
    }
}
