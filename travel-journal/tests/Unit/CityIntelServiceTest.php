<?php

namespace Tests\Unit;

use App\Models\City;
use App\Models\CityIntel;
use App\Models\ElectricalStandard;
use App\Models\EmergencyContact;
use App\Models\User;
use App\Services\CityIntelService;
use App\Services\CostOfLivingEstimator;
use App\Services\ElectricalStandardRepository;
use App\Services\GeoLookupService;
use App\Services\Intel\LonelyPlanetIntelProvider;
use App\Services\TimeZoneHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class CityIntelServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_intel_is_cached_and_created_from_provider(): void
    {
        config(['cache.default' => 'array']);
        Cache::flush();

        $city = City::factory()->create(['country_code' => 'AE', 'timezone' => 'Asia/Dubai']);

        $provider = Mockery::mock(LonelyPlanetIntelProvider::class);
        $provider->shouldReceive('fetch')
            ->once()
            ->withArgs(fn ($arg) => $arg instanceof City && $arg->is($city))
            ->andReturn([
                'summary' => 'A great city.',
                'checklist' => ['Adapter', 'Sunscreen'],
                'cultural_notes' => ['Dress modestly'],
                'budget' => ['daily' => 100],
            ]);

        $service = new CityIntelService(
            Mockery::mock(GeoLookupService::class),
            Mockery::mock(TimeZoneHelper::class),
            Mockery::mock(ElectricalStandardRepository::class),
            Mockery::mock(CostOfLivingEstimator::class),
            $provider,
        );

        $first = $service->intel($city);
        $this->assertInstanceOf(CityIntel::class, $first);
        $this->assertSame($city->id, $first->city_id);
        $this->assertSame('A great city.', $first->summary);

        $second = $service->intel($city);
        $this->assertTrue($second->is($first));

        $this->assertDatabaseCount('city_intel', 1);
    }

    public function test_dashboard_payload_includes_time_electrical_emergency_and_budget(): void
    {
        config(['cache.default' => 'array']);
        Cache::flush();

        $city = City::factory()->create([
            'country_code' => 'AE',
            'timezone' => 'Asia/Dubai',
        ]);

        $intel = CityIntel::create([
            'city_id' => $city->id,
            'summary' => 'Already known',
            'budget' => [],
        ]);

        EmergencyContact::create([
            'country_code' => 'AE',
            'service' => 'Police',
            'number' => '999',
        ]);

        $user = User::factory()->create();

        $tz = Mockery::mock(TimeZoneHelper::class);
        $tz->shouldReceive('nowIn')
            ->andReturnUsing(fn () => Carbon::parse('2025-01-01 10:00:00', 'UTC'));
        $tz->shouldReceive('diffInHours')->andReturn(4);

        $standard = ElectricalStandard::create([
            'country_code' => 'AE',
            'plug_types' => 'G',
            'voltage' => '230V',
            'frequency' => '50Hz',
        ]);

        $electrical = Mockery::mock(ElectricalStandardRepository::class);
        $electrical->shouldReceive('forCountry')->with('AE')->andReturn($standard);

        $cost = Mockery::mock(CostOfLivingEstimator::class);
        $cost->shouldReceive('estimate')
            ->once()
            ->andReturn([
                'low' => '$100',
                'mid' => '$200',
                'high' => '$300',
            ]);

        $provider = Mockery::mock(LonelyPlanetIntelProvider::class);
        $provider->shouldReceive('fetch')->never();

        $service = new CityIntelService(
            Mockery::mock(GeoLookupService::class),
            $tz,
            $electrical,
            $cost,
            $provider,
        );

        $payload = $service->dashboardPayload($city->fresh('intel'), $user);

        $this->assertSame($city->id, $payload['city']['id']);
        $this->assertSame($intel->id, $payload['intel']['id']);
        $this->assertSame('Asia/Dubai', $payload['time']['timezone']);
        $this->assertSame(4, $payload['time']['offset_hours']);
        $this->assertSame('G', $payload['electrical']->plug_types);
        $this->assertSame('Police', $payload['emergency_contacts'][0]['service']);
        $this->assertSame('$200', $payload['budget']['mid']);
    }
}
