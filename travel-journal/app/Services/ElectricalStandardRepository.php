<?php

namespace App\Services;

use App\Models\ElectricalStandard;
use Illuminate\Support\Facades\Cache;

class ElectricalStandardRepository
{
    public function forCountry(?string $countryCode): ?ElectricalStandard
    {
        if (! $countryCode) {
            return null;
        }

        return Cache::rememberForever("electrical_standard_{$countryCode}", function () use ($countryCode) {
            return ElectricalStandard::where('country_code', strtoupper($countryCode))->first();
        });
    }
}
