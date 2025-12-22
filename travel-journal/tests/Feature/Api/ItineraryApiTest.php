<?php

namespace Tests\Feature\Api;

use App\Models\City;
use App\Models\Itinerary;
use App\Models\ItineraryItem;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ItineraryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_create_and_update_itineraries(): void
    {
        $user = User::factory()->create(['password' => 'password']);
        $trip = Trip::factory()->for($user)->create([
            'start_date' => Carbon::parse('2025-01-10'),
            'end_date' => Carbon::parse('2025-01-15'),
        ]);

        $city = City::factory()->create();

        $token = $this->issueToken($user, ['itinerary:read', 'itinerary:write']);

        $this->withToken($token)
            ->getJson('/api/trips/'.$trip->id.'/itineraries')
            ->assertOk()
            ->assertExactJson(['data' => []]);

        $created = $this->withToken($token)
            ->postJson('/api/trips/'.$trip->id.'/itineraries', [
                'title' => 'Main plan',
                'city_id' => $city->id,
                'start_date' => '2025-01-12',
                'end_date' => '2025-01-14',
                'is_primary' => true,
                'theme' => 'desert',
                'metadata' => ['vibe' => 'chill'],
            ])
            ->assertCreated()
            ->json('data');

        $this->assertSame('Main plan', $created['title']);
        $this->assertSame(3, $created['day_count']);
        $this->assertTrue($created['is_primary']);

        $itineraryId = $created['id'];

        // Attach a couple items and ensure index returns them ordered by start_datetime.
        ItineraryItem::factory()->for($trip)->create([
            'itinerary_id' => $itineraryId,
            'start_datetime' => Carbon::parse('2025-01-13 12:00:00'),
            'type' => 'activity',
            'title' => 'Later',
        ]);
        ItineraryItem::factory()->for($trip)->create([
            'itinerary_id' => $itineraryId,
            'start_datetime' => Carbon::parse('2025-01-13 09:00:00'),
            'type' => 'activity',
            'title' => 'Earlier',
        ]);

        $list = $this->withToken($token)
            ->getJson('/api/trips/'.$trip->id.'/itineraries')
            ->assertOk()
            ->json('data');

        $this->assertCount(1, $list);
        $this->assertSame('Earlier', $list[0]['items'][0]['title']);
        $this->assertSame('Later', $list[0]['items'][1]['title']);

        $updated = $this->withToken($token)
            ->putJson('/api/trips/'.$trip->id.'/itineraries/'.$itineraryId, [
                'start_date' => '2025-01-11',
                'end_date' => '2025-01-15',
            ])
            ->assertOk()
            ->json('data');

        $this->assertSame(5, $updated['day_count']);

        $this->assertDatabaseHas('itineraries', [
            'id' => $itineraryId,
            'trip_id' => $trip->id,
            'day_count' => 5,
        ]);
    }

    public function test_cannot_update_itinerary_from_other_trip(): void
    {
        $user = User::factory()->create(['password' => 'password']);
        $tripA = Trip::factory()->for($user)->create();
        $tripB = Trip::factory()->for($user)->create();

        $itinerary = Itinerary::factory()->for($tripA)->create();

        $token = $this->issueToken($user, ['itinerary:write']);

        $this->withToken($token)
            ->putJson('/api/trips/'.$tripB->id.'/itineraries/'.$itinerary->id, ['title' => 'Nope'])
            ->assertForbidden();
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
