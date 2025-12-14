<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItineraryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'trip_id' => $this->trip_id,
            'city_id' => $this->city_id,
            'title' => $this->title,
            'day_count' => $this->day_count,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'is_primary' => $this->is_primary,
            'theme' => $this->theme,
            'metadata' => $this->metadata,
            'city' => new CityResource($this->whenLoaded('city')),
            'items' => ItineraryItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
