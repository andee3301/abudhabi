<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WeatherSnapshotResource extends JsonResource
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
            'trip_id' => $this->trip_id,
            'provider' => $this->provider,
            'temperature' => $this->temperature,
            'humidity' => $this->humidity,
            'wind_speed' => $this->wind_speed,
            'conditions' => $this->conditions,
            'icon' => $this->icon,
            'recorded_at' => $this->recorded_at,
            'payload' => $this->payload,
        ];
    }
}
