<?php

namespace Tests\Feature\Api;

use App\Models\Trip;
use App\Models\TripEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripEventApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_crud_trip_events(): void
    {
        $user = User::factory()->create(['password' => 'password']);
        $trip = Trip::factory()->for($user)->create();

        $token = $this->issueToken($user, ['events:read', 'events:write']);

        $this->withToken($token)
            ->getJson('/api/trips/'.$trip->id.'/events')
            ->assertOk()
            ->assertExactJson([]);

        $created = $this->withToken($token)
            ->postJson('/api/trips/'.$trip->id.'/events', [
                'type' => 'location',
                'title' => 'Arrive in city',
                'description' => 'Landing and hotel transfer',
                'start_time' => now()->toIso8601String(),
            ])
            ->assertCreated()
            ->json();

        $this->assertSame(1, $created['position']);

        $eventId = $created['id'];

        $this->withToken($token)
            ->putJson('/api/trips/'.$trip->id.'/events/'.$eventId, [
                'title' => 'Arrive in Lisbon',
            ])
            ->assertOk()
            ->assertJsonPath('title', 'Arrive in Lisbon');

        $this->assertDatabaseHas('trip_events', [
            'id' => $eventId,
            'trip_id' => $trip->id,
            'title' => 'Arrive in Lisbon',
        ]);

        $this->withToken($token)
            ->deleteJson('/api/trips/'.$trip->id.'/events/'.$eventId)
            ->assertNoContent();

        $this->assertDatabaseMissing('trip_events', [
            'id' => $eventId,
        ]);
    }

    public function test_event_must_belong_to_trip(): void
    {
        $user = User::factory()->create(['password' => 'password']);
        $tripA = Trip::factory()->for($user)->create();
        $tripB = Trip::factory()->for($user)->create();

        $event = TripEvent::factory()->for($tripA)->create();

        $token = $this->issueToken($user, ['events:write']);

        $this->withToken($token)
            ->putJson('/api/trips/'.$tripB->id.'/events/'.$event->id, ['title' => 'Nope'])
            ->assertNotFound();
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
