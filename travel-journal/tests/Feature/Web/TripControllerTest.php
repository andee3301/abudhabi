<?php

namespace Tests\Feature\Web;

use App\Models\City;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_trip_show_renders_for_owner_and_maps_city_stops(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $city = City::factory()->create([
            'name' => 'Berlin',
            'slug' => 'berlin',
            'country_code' => 'DE',
        ]);

        $trip = Trip::factory()->for($user)->create([
            'city_stops' => [
                ['city_id' => $city->id],
                ['label' => 'Custom stop', 'country_code' => 'DE'],
            ],
        ]);

        $response = $this->actingAs($user)
            ->get('/trips/'.$trip->id)
            ->assertOk()
            ->assertViewIs('trips.show')
            ->assertViewHas('trip')
            ->assertViewHas('cityStops');

        $stops = $response->viewData('cityStops');
        $this->assertCount(2, $stops);
        $this->assertEquals($city->display_name ?? $city->name, $stops->first()['label']);
        $this->assertEquals('Custom stop', $stops->get(1)['label']);
    }

    public function test_trip_show_forbids_non_owner(): void
    {
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $other = User::factory()->create(['email_verified_at' => now()]);

        $trip = Trip::factory()->for($owner)->create();

        $this->actingAs($other)
            ->get('/trips/'.$trip->id)
            ->assertForbidden();
    }
}
