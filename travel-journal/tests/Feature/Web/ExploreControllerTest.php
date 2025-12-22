<?php

namespace Tests\Feature\Web;

use App\Models\City;
use App\Models\User;
use App\Services\CityIntelService;
use App\Services\GeoLookupService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExploreControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_explore_page_renders_with_destination_and_suggestions(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $city = City::factory()->create([
            'name' => 'Kyoto',
            'slug' => 'kyoto',
            'country_code' => 'JP',
        ]);

        $this->mock(GeoLookupService::class, function ($mock) use ($city) {
            $mock->shouldReceive('findBySlugOrName')
                ->once()
                ->with('Kyoto')
                ->andReturn($city);

            $mock->shouldReceive('search')
                ->once()
                ->with('Kyoto', 6)
                ->andReturn(new EloquentCollection([$city]));
        });

        $this->mock(CityIntelService::class, function ($mock) {
            $mock->shouldReceive('dashboardPayload')
                ->once()
                ->andReturn(['ok' => true]);
        });

        $this->actingAs($user)
            ->get('/explore?q=Kyoto')
            ->assertOk()
            ->assertViewIs('explore.index')
            ->assertViewHas('destination', 'Kyoto')
            ->assertViewHas('city')
            ->assertViewHas('intel')
            ->assertViewHas('suggestions')
            ->assertViewHas('catalog');
    }
}
