<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItineraryItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'trip_id' => $this->trip_id,
            'type' => $this->type,
            'title' => $this->title,
            'start_datetime' => $this->start_datetime,
            'end_datetime' => $this->end_datetime,
            'location_name' => $this->location_name,
            'address' => $this->address,
            'price' => $this->price,
            'currency' => $this->currency,
            'status' => $this->status,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
