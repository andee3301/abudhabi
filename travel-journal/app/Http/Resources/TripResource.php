<?php

namespace App\Http\Resources;

use App\Models\City;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $cityRelation = $this->resource instanceof Model && $this->resource->relationLoaded('city')
            ? $this->resource->getRelation('city')
            : null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'primary_location_name' => $this->primary_location_name,
            'city_id' => $this->city_id,
            'city' => $this->city,
            'state_region' => $this->state_region,
            'country_code' => $this->country_code,
            'timezone' => $this->timezone,
            'region_id' => $this->region_id,
            'location_label' => $this->location_label,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'status' => $this->status,
            'companion_name' => $this->companion_name,
            'notes' => $this->notes,
            'cover_image_url' => $this->cover_image_url,
            'tags' => $this->tags,
            'region' => new RegionResource($this->whenLoaded('region')),
            'city_resource' => $cityRelation instanceof City ? new CityResource($cityRelation) : null,
            'itinerary_items' => ItineraryItemResource::collection($this->whenLoaded('itineraryItems')),
            'itineraries' => ItineraryResource::collection($this->whenLoaded('itineraries')),
            'journal_entries' => JournalEntryResource::collection($this->whenLoaded('journalEntries')),
            'country_visits' => CountryVisitResource::collection($this->whenLoaded('countryVisits')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
