<?php

namespace Tests\Feature\Api;

use App\Models\ItineraryItem;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItineraryItemApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_itinerary_item_and_defaults_are_applied(): void
    {
        $user = User::factory()->create(['password' => 'password']);
        $trip = Trip::factory()->for($user)->create([
            'city' => 'Lisbon',
            'country_code' => 'PT',
            'timezone' => 'Europe/Lisbon',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
        ]);

        $token = $this->issueToken($user, ['itinerary:write']);

        $start = now()->addDays(2)->setTime(10, 0)->toIso8601String();

        $this->withToken($token)
            ->postJson('/api/trips/'.$trip->id.'/itinerary', [
                'type' => 'housing',
                'title' => 'Hotel check-in',
                'start_datetime' => $start,
            ])
            ->assertCreated()
            ->assertJsonPath('data.trip_id', $trip->id)
            ->assertJsonPath('data.type', 'housing')
            ->assertJsonPath('data.title', 'Hotel check-in')
            ->assertJsonPath('data.country_code', 'PT')
            ->assertJsonPath('data.timezone', 'Europe/Lisbon')
            ->assertJsonPath('data.day_number', 3);

        $this->assertDatabaseHas('itinerary_items', [
            'trip_id' => $trip->id,
            'title' => 'Hotel check-in',
            'country_code' => 'PT',
            'timezone' => 'Europe/Lisbon',
            'day_number' => 3,
        ]);
    }

    public function test_can_filter_itinerary_items_by_type(): void
    {
        $user = User::factory()->create(['password' => 'password']);
        $trip = Trip::factory()->for($user)->create();

        ItineraryItem::factory()->for($trip)->create(['type' => 'housing', 'title' => 'Hotel']);
        ItineraryItem::factory()->for($trip)->create(['type' => 'activity', 'title' => 'Museum']);

        $token = $this->issueToken($user, ['itinerary:read']);

        $response = $this->withToken($token)
            ->getJson('/api/trips/'.$trip->id.'/itinerary?type=housing')
            ->assertOk()
            ->json('data');

        $this->assertCount(1, $response);
        $this->assertSame('housing', $response[0]['type']);
        $this->assertSame('Hotel', $response[0]['title']);
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
