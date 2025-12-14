<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryVisitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'trip_id' => $this->trip_id,
            'country_code' => $this->country_code,
            'city_name' => $this->city_name,
            'state_region' => $this->state_region,
            'timezone' => $this->timezone,
            'region_id' => $this->region_id,
            'region' => new RegionResource($this->whenLoaded('region')),
            'visited_at' => $this->visited_at,
        ];
    }
}
