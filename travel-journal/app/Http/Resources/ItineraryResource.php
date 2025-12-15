<?php

namespace App\Http\Resources;

use App\Models\City;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItineraryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $cityRelation = $this->resource instanceof Model && $this->resource->relationLoaded('city')
            ? $this->resource->getRelation('city')
            : null;

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
            'city' => $cityRelation instanceof City ? new CityResource($cityRelation) : null,
            'items' => ItineraryItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
