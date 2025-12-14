<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityIntelResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'city_id' => $this->city_id,
            'tagline' => $this->tagline,
            'summary' => $this->summary,
            'local_time_label' => $this->local_time_label,
            'currency_code' => $this->currency_code,
            'currency_rate' => $this->currency_rate,
            'electrical_plugs' => $this->electrical_plugs,
            'voltage' => $this->voltage,
            'emergency_numbers' => $this->emergency_numbers,
            'neighborhoods' => $this->neighborhoods,
            'checklist' => $this->checklist,
            'cultural_notes' => $this->cultural_notes,
            'weather' => $this->weather,
            'visa' => $this->visa,
            'best_months' => $this->best_months,
            'transport' => $this->transport,
            'budget' => $this->budget,
            'seasonality' => $this->seasonality,
            'meta' => $this->meta,
        ];
    }
}
