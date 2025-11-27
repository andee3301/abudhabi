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
            'destination' => $this->destination,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'status' => $this->status,
            'notes' => $this->notes,
            'timezone' => $this->timezone,
            'cover_image_path' => $this->cover_image_path,
            'latest_weather' => new WeatherSnapshotResource($this->whenLoaded('latestWeather')),
            'weather' => WeatherSnapshotResource::collection($this->whenLoaded('weatherSnapshots')),
            'journal_entries' => JournalEntryResource::collection($this->whenLoaded('journalEntries')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
