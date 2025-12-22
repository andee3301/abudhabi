<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Trips\ManageTrips;
use App\Models\Region;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageTripsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_trip_with_stops_and_wishlist(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $region = Region::factory()->create([
            'country_code' => 'FR',
            'name' => 'Île-de-France',
            'default_timezone' => 'Europe/Paris',
        ]);

        Livewire::test(ManageTrips::class)
            ->set('title', 'France Tour')
            ->set('primary_location_name', 'Paris, France')
            ->set('country_code', 'FR')
            ->set('timezone', 'Europe/Paris')
            ->set('region_id', $region->id)
            ->set('start_date', now()->addDay()->toDateString())
            ->set('end_date', now()->addDays(4)->toDateString())
            ->set('formStatus', 'ongoing')
            ->set('cityStopsInput', "Paris (FR), Lyon - FR")
            ->set('wishlistInput', "Louvre, Eiffel Tower")
            ->call('createTrip')
            ->assertDispatched('tripCreated');

        $trip = Trip::query()->where('user_id', $user->id)->latest('id')->firstOrFail();

        $this->assertSame('France Tour', $trip->title);
        $this->assertSame('ongoing', $trip->status);
        $this->assertSame(['Louvre', 'Eiffel Tower'], $trip->wishlist_locations);
        $this->assertCount(2, $trip->city_stops);
        $this->assertSame('Paris', $trip->city_stops[0]['label']);
        $this->assertSame('FR', $trip->city_stops[0]['country_code']);
        $this->assertNotEmpty($trip->city_stops[0]['city_id']);
    }

    public function test_mark_ongoing_sets_other_ongoing_trips_to_planned(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $existingOngoing = Trip::factory()->for($user)->create(['status' => 'ongoing']);
        $target = Trip::factory()->for($user)->create(['status' => 'planned']);

        Livewire::test(ManageTrips::class)
            ->call('markOngoing', $target->id);

        $this->assertDatabaseHas('trips', ['id' => $existingOngoing->id, 'status' => 'planned']);
        $this->assertDatabaseHas('trips', ['id' => $target->id, 'status' => 'ongoing']);
    }

    public function test_user_can_edit_and_update_existing_trip(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $trip = Trip::factory()->for($user)->create([
            'title' => 'Original',
            'primary_location_name' => 'Rome, Italy',
            'country_code' => 'IT',
            'timezone' => 'Europe/Rome',
            'status' => 'planned',
        ]);

        Livewire::test(ManageTrips::class)
            ->call('startEditing', $trip->id)
            ->assertSet('editingTripId', $trip->id)
            ->set('title', 'Updated')
            ->call('updateTrip');

        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'title' => 'Updated',
        ]);
    }
}
