<?php

namespace App\Services;

use App\Models\City;
use App\Models\CityIntel;

class CostOfLivingEstimator
{
    protected array $baselines = [
        'USD' => [120, 210, 320],
        'EUR' => [100, 180, 260],
        'GBP' => [90, 170, 240],
        'JPY' => [12000, 22000, 32000],
        'THB' => [1200, 2200, 4200],
    ];

    public function estimate(?City $city = null, ?CityIntel $intel = null): array
    {
        $currency = $intel?->currency_code ?? $city?->currency_code ?? 'USD';
        $baseline = $this->baselines[$currency] ?? $this->baselines['USD'];

        return [
            'low' => $this->format($baseline[0], $currency),
            'mid' => $this->format($baseline[1], $currency),
            'high' => $this->format($baseline[2], $currency),
        ];
    }

    protected function format(float|int $amount, string $currency): string
    {
        $symbol = $this->symbolFor($currency);

        return $symbol.number_format($amount);
    }

    protected function symbolFor(string $currency): string
    {
        return match (strtoupper($currency)) {
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'THB' => '฿',
            default => '$',
        };
    }
}
