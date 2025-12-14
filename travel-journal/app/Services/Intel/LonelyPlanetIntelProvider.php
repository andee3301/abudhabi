<?php

namespace App\Services\Intel;

use App\Models\City;

class LonelyPlanetIntelProvider
{
    /**
     * Placeholder stub for future external API integration.
     */
    public function fetch(City $city): array
    {
        return [
            'summary' => "Arrival intel for {$city->name} coming soon.",
            'checklist' => [
                'Download offline maps.',
                'Confirm airport transfer options.',
                'Add emergency contacts to your phone.',
            ],
            'cultural_notes' => [
                'Respect local customs; research greetings and tipping.',
            ],
        ];
    }
}
