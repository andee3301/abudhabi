<?php

namespace App\Services;

use App\Http\Resources\CityIntelResource;
use App\Http\Resources\CityResource;
use App\Models\City;
use App\Models\CityIntel;
use App\Models\EmergencyContact;
use App\Models\User;
use App\Services\Intel\LonelyPlanetIntelProvider;
use Illuminate\Support\Facades\Cache;

class CityIntelService
{
    public function __construct(
        protected GeoLookupService $geoLookup,
        protected TimeZoneHelper $timeZoneHelper,
        protected ElectricalStandardRepository $electricalRepository,
        protected CostOfLivingEstimator $costEstimator,
        protected LonelyPlanetIntelProvider $lonelyPlanetIntelProvider,
    ) {}

    public function intel(City $city): CityIntel
    {
        return Cache::remember("city_intel_{$city->id}", 1800, function () use ($city) {
            if ($city->intel) {
                return $city->intel;
            }

            $fetched = $this->lonelyPlanetIntelProvider->fetch($city);

            return CityIntel::create([
                'city_id' => $city->id,
                'summary' => $fetched['summary'] ?? 'Intel coming soon.',
                'checklist' => $fetched['checklist'] ?? [],
                'cultural_notes' => $fetched['cultural_notes'] ?? [],
                'budget' => $fetched['budget'] ?? [],
            ]);
        });
    }

    public function dashboardPayload(City $city, ?User $user = null): array
    {
        $intel = $this->intel($city);
        $homeTimezone = $user?->homeSettings?->home_timezone ?? $user?->timezone ?? 'UTC';
        $electrical = $this->electricalRepository->forCountry($city->country_code);
        $budget = $intel->budget ?: $this->costEstimator->estimate($city, $intel);
        $homeCurrency = $user?->homeSettings?->preferred_currency;

        $time = [
            'local_time' => $this->timeZoneHelper->nowIn($city->timezone)->toIso8601String(),
            'home_time' => $homeTimezone ? $this->timeZoneHelper->nowIn($homeTimezone)->toIso8601String() : null,
            'offset_hours' => $this->timeZoneHelper->diffInHours($homeTimezone, $city->timezone),
            'timezone' => $city->timezone,
            'home_timezone' => $homeTimezone,
        ];

        $emergencyContacts = EmergencyContact::where('country_code', $city->country_code)
            ->orderBy('service')
            ->get(['service', 'number', 'note'])
            ->toArray();

        return [
            'city' => CityResource::make($city)->resolve(),
            'intel' => CityIntelResource::make($intel)->resolve(),
            'time' => $time,
            'electrical' => $electrical,
            'emergency_contacts' => $emergencyContacts,
            'budget' => $budget,
            'home_currency' => $homeCurrency,
        ];
    }
}
