<?php

namespace Tests\Feature\Web;

use App\Models\City;
use App\Models\User;
use App\Services\CityIntelService;
use App\Services\GeoLookupService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CityLookupControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_city_search_returns_city_resource_collection(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $city = City::factory()->create([
            'name' => 'Tokyo',
            'slug' => 'tokyo',
            'country_code' => 'JP',
        ]);

        $this->mock(GeoLookupService::class, function ($mock) use ($city) {
            $mock->shouldReceive('search')
                ->once()
                ->with('to', 10)
                ->andReturn(new EloquentCollection([$city]));
        });

        $this->actingAs($user)
            ->getJson('/cities/search?q=to')
            ->assertOk()
            ->assertJsonPath('data.0.id', $city->id)
            ->assertJsonPath('data.0.name', 'Tokyo');
    }

    public function test_city_show_returns_dashboard_payload_json(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $city = City::factory()->create([
            'name' => 'Osaka',
            'slug' => 'osaka',
            'country_code' => 'JP',
        ]);

        $this->mock(CityIntelService::class, function ($mock) {
            $mock->shouldReceive('dashboardPayload')
                ->once()
                ->andReturn(['foo' => 'bar']);
        });

        $this->actingAs($user)
            ->getJson('/cities/'.$city->slug)
            ->assertOk()
            ->assertJson(['foo' => 'bar']);
    }
}
