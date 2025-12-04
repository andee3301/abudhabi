<?php

namespace App\Http\Resources;

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
        return [
            'id' => $this->id,
            'title' => $this->title,
            'primary_location_name' => $this->primary_location_name,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'status' => $this->status,
            'companion_name' => $this->companion_name,
            'notes' => $this->notes,
            'cover_image_url' => $this->cover_image_url,
            'itinerary_items' => ItineraryItemResource::collection($this->whenLoaded('itineraryItems')),
            'journal_entries' => JournalEntryResource::collection($this->whenLoaded('journalEntries')),
            'country_visits' => CountryVisitResource::collection($this->whenLoaded('countryVisits')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
