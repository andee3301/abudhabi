<?php

namespace Tests\Feature\Web;

use App\Models\City;
use App\Models\Trip;
use App\Models\User;
use App\Services\CityIntelService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CityGuideControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_city_guide_renders_with_related_trips(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $city = City::factory()->create([
            'name' => 'Lisbon',
            'slug' => 'lisbon',
            'country_code' => 'PT',
        ]);

        $directTrip = Trip::factory()->for($user)->create([
            'city_id' => $city->id,
            'start_date' => now()->subWeek(),
            'end_date' => now()->subDays(2),
            'status' => 'completed',
        ]);

        $stopTrip = Trip::factory()->for($user)->create([
            'city_id' => null,
            'city_stops' => [
                ['city_id' => $city->id],
            ],
            'start_date' => now()->subWeeks(2),
            'end_date' => now()->subWeek(),
            'status' => 'completed',
        ]);

        $this->mock(CityIntelService::class, function ($mock) {
            $mock->shouldReceive('dashboardPayload')
                ->once()
                ->andReturn(['ok' => true]);
        });

        $response = $this->actingAs($user)
            ->get('/city-guides/'.$city->slug)
            ->assertOk()
            ->assertViewIs('cities.show')
            ->assertViewHas('city')
            ->assertViewHas('intel')
            ->assertViewHas('relatedTrips');

        $relatedTrips = $response->viewData('relatedTrips');
        $this->assertCount(2, $relatedTrips);
        $this->assertTrue($relatedTrips->pluck('id')->contains($directTrip->id));
        $this->assertTrue($relatedTrips->pluck('id')->contains($stopTrip->id));
    }
}
