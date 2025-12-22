<?php

namespace Tests\Feature\Api;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class TripApiCoverageTest extends TestCase
{
    use RefreshDatabase;

    private function makeToken(User $user, array $abilities): string
    {
        return $user->createToken('phpunit', $abilities)->plainTextToken;
    }

    public function test_trip_index_supports_search_and_status_filters(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $match = Trip::factory()->for($user)->create([
            'title' => 'Kyoto weekend',
            'status' => 'planned',
            'start_date' => now()->addDays(10),
        ]);

        Trip::factory()->for($user)->create([
            'title' => 'Berlin trip',
            'status' => 'completed',
            'start_date' => now()->subDays(10),
            'end_date' => now()->subDays(3),
        ]);

        $token = $this->makeToken($user, ['trips:read', 'trips:write']);

        $this->withToken($token)->getJson('/api/trips?search=Kyoto')
            ->assertOk()
            ->assertJsonPath('data.0.id', $match->id);

        $this->withToken($token)->getJson('/api/trips?status=planned')
            ->assertOk()
            ->assertJsonFragment(['id' => $match->id])
            ->assertJsonMissing(['title' => 'Berlin trip']);
    }

    public function test_trip_show_update_and_destroy_enforce_ownership(): void
    {
        $owner = User::factory()->create(['password' => 'password']);
        $other = User::factory()->create(['password' => 'password']);

        $trip = Trip::factory()->for($owner)->create([
            'title' => 'Original',
            'status' => 'planned',
            'start_date' => now()->addWeek(),
            'end_date' => now()->addWeeks(2),
        ]);

        $token = $this->makeToken($owner, ['trips:read', 'trips:write']);

        $this->withToken($token)->getJson('/api/trips/'.$trip->id)
            ->assertOk()
            ->assertJsonPath('data.id', $trip->id);

        $this->withToken($token)->putJson('/api/trips/'.$trip->id, [
                'title' => 'Updated',
                'end_date' => now()->addWeeks(3)->toDateString(),
            ])
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated');

        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'title' => 'Updated',
        ]);

        $this->withToken($token)->deleteJson('/api/trips/'.$trip->id)
            ->assertNoContent();

        $this->assertDatabaseMissing('trips', [
            'id' => $trip->id,
        ]);

        $otherToken = $this->makeToken($other, ['trips:read', 'trips:write']);
        $foreignTrip = Trip::factory()->create(['user_id' => $owner->id]);

        $this->assertSame($owner->id, $foreignTrip->user_id);

        // Sanctum's guard instance can cache the authenticated user within the same PHP process.
        // Clear guard caches before switching tokens to avoid leaking auth state between requests.
        Auth::forgetGuards();

        $this->withToken($otherToken)->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('id', $other->id);

        $this->withToken($otherToken)->getJson('/api/trips/'.$foreignTrip->id)
            ->assertForbidden();

        $this->withToken($otherToken)->putJson('/api/trips/'.$foreignTrip->id, ['title' => 'Nope'])
            ->assertForbidden();

        $this->withToken($otherToken)->deleteJson('/api/trips/'.$foreignTrip->id)
            ->assertForbidden();
    }
}
