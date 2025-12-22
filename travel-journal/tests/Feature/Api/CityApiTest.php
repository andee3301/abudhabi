<?php

namespace Tests\Feature\Api;

use App\Models\City;
use App\Models\CityIntel;
use App\Models\User;
use App\Services\CityIntelService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CityApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_search_cities(): void
    {
        $user = User::factory()->create(['password' => 'password']);
        $token = $this->issueToken($user, ['cities:read']);

        City::factory()->create(['name' => 'Lisbon', 'country_code' => 'PT', 'state_region' => 'Lisbon']);
        City::factory()->create(['name' => 'Porto', 'country_code' => 'PT']);
        City::factory()->create(['name' => 'Kyoto', 'country_code' => 'JP']);

        $this->withToken($token)
            ->getJson('/api/cities?q=PT&limit=2')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['name' => 'Lisbon']);
    }

    public function test_can_show_city_dashboard_payload(): void
    {
        $user = User::factory()->create(['password' => 'password']);
        $token = $this->issueToken($user, ['cities:read']);

        $city = City::factory()->create(['name' => 'Lisbon', 'country_code' => 'PT']);

        $this->mock(CityIntelService::class, function ($mock) use ($city, $user) {
            $mock->shouldReceive('dashboardPayload')
                ->once()
                ->withArgs(function ($passedCity, $passedUser) use ($city, $user) {
                    return $passedCity->is($city) && $passedUser->is($user);
                })
                ->andReturn([
                    'city' => ['id' => $city->id, 'name' => $city->name],
                    'intel' => ['summary' => 'Hello'],
                    'time' => ['timezone' => 'UTC'],
                    'electrical' => [],
                    'emergency_contacts' => [],
                    'budget' => [],
                    'home_currency' => null,
                ]);
        });

        $this->withToken($token)
            ->getJson('/api/cities/'.$city->slug)
            ->assertOk()
            ->assertJsonPath('city.name', 'Lisbon')
            ->assertJsonPath('intel.summary', 'Hello');
    }

    public function test_can_get_city_intel_resource(): void
    {
        $user = User::factory()->create(['password' => 'password']);
        $token = $this->issueToken($user, ['cities:read']);

        $city = City::factory()->create(['name' => 'Lisbon', 'country_code' => 'PT']);

        $intel = CityIntel::create([
            'city_id' => $city->id,
            'tagline' => 'Tag',
            'summary' => 'Summary',
            'checklist' => ['pack'],
            'cultural_notes' => [],
            'budget' => [],
        ])->fresh();

        $this->mock(CityIntelService::class, function ($mock) use ($city, $intel) {
            $mock->shouldReceive('intel')->once()->withArgs(function ($passedCity) use ($city) {
                return $passedCity->is($city);
            })->andReturn($intel);
        });

        $this->withToken($token)
            ->getJson('/api/cities/'.$city->slug.'/intel')
            ->assertOk()
            ->assertJsonPath('data.summary', 'Summary')
            ->assertJsonPath('city.name', 'Lisbon');
    }

    private function issueToken(User $user, array $abilities): string
    {
        return $this->postJson('/api/auth/token', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'phpunit',
            'abilities' => $abilities,
        ])->assertOk()->json('token');
    }
}
