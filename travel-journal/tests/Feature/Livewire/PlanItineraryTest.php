<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Trips\PlanItinerary;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PlanItineraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_itinerary_item_with_timezone(): void
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->for($user)->create([
            'city' => 'Lisbon',
            'country_code' => 'PT',
            'timezone' => 'Europe/Lisbon',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(5),
        ]);

        $this->actingAs($user);

        Livewire::test(PlanItinerary::class, ['trip' => $trip])
            ->set('title', 'Check-in at hotel')
            ->set('type', 'housing')
            ->set('city', 'Lisbon')
            ->set('country_code', 'PT')
            ->set('timezone', 'Europe/Lisbon')
            ->set('start_datetime', now()->addDay()->format('Y-m-d\TH:i'))
            ->call('addItem')
            ->assertDispatched('itineraryUpdated');

        $this->assertDatabaseHas('itinerary_items', [
            'trip_id' => $trip->id,
            'title' => 'Check-in at hotel',
            'timezone' => 'Europe/Lisbon',
        ]);
    }
}
