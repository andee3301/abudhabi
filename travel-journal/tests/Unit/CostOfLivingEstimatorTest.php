<?php

namespace Tests\Unit;

use App\Models\City;
use App\Models\CityIntel;
use App\Services\CostOfLivingEstimator;
use PHPUnit\Framework\TestCase;

class CostOfLivingEstimatorTest extends TestCase
{
    private CostOfLivingEstimator $estimator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->estimator = new CostOfLivingEstimator();
    }

    public function test_defaults_to_usd_when_no_city_or_intel_is_provided(): void
    {
        $estimate = $this->estimator->estimate();

        $this->assertSame([
            'low' => '$120',
            'mid' => '$210',
            'high' => '$320',
        ], $estimate);
    }

    public function test_prefers_city_currency_when_intel_is_absent(): void
    {
        $city = new City(['currency_code' => 'EUR']);

        $estimate = $this->estimator->estimate($city);

        $this->assertSame([
            'low' => '€100',
            'mid' => '€180',
            'high' => '€260',
        ], $estimate);
    }

    public function test_city_intel_currency_overrides_city_currency(): void
    {
        $city = new City(['currency_code' => 'THB']);
        $intel = new CityIntel(['currency_code' => 'GBP']);

        $estimate = $this->estimator->estimate($city, $intel);

        $this->assertSame([
            'low' => '£90',
            'mid' => '£170',
            'high' => '£240',
        ], $estimate);
    }

    public function test_unknown_currency_falls_back_to_usd_baseline(): void
    {
        $city = new City(['currency_code' => 'aud']);

        $estimate = $this->estimator->estimate($city);

        $this->assertSame('$120', $estimate['low']);
        $this->assertSame('$210', $estimate['mid']);
        $this->assertSame('$320', $estimate['high']);
    }
}
