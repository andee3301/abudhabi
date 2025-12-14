<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'country_code' => $this->country_code,
            'state_region' => $this->state_region,
            'timezone' => $this->timezone,
            'currency_code' => $this->currency_code,
            'primary_language' => $this->primary_language,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'hero_image_url' => $this->hero_image_url,
            'accent_color' => $this->accent_color,
            'meta' => $this->meta,
        ];
    }
}
