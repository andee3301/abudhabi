<?php

namespace App\Http\Resources;

use App\Models\City;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItineraryItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $cityRelation = $this->resource instanceof Model && $this->resource->relationLoaded('city')
            ? $this->resource->getRelation('city')
            : null;

        return [
            'id' => $this->id,
            'trip_id' => $this->trip_id,
            'itinerary_id' => $this->itinerary_id,
            'city_id' => $this->city_id,
            'type' => $this->type,
            'title' => $this->title,
            'start_datetime' => $this->start_datetime,
            'end_datetime' => $this->end_datetime,
            'day_number' => $this->day_number,
            'sort_order' => $this->sort_order,
            'location_name' => $this->location_name,
            'city' => $this->city,
            'state_region' => $this->state_region,
            'country_code' => $this->country_code,
            'timezone' => $this->timezone,
            'region_id' => $this->region_id,
            'region' => new RegionResource($this->whenLoaded('region')),
            'address' => $this->address,
            'price' => $this->price,
            'currency' => $this->currency,
            'status' => $this->status,
            'metadata' => $this->metadata,
            'links' => $this->links,
            'tags' => $this->tags,
            'city_resource' => $cityRelation instanceof City ? new CityResource($cityRelation) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
