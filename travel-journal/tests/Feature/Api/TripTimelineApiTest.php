<?php

namespace Tests\Feature\Api;

use App\Models\Trip;
use App\Models\TripTimeline;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TripTimelineApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_crud_timeline_entries(): void
    {
        $user = User::factory()->create(['password' => 'password']);
        $trip = Trip::factory()->for($user)->create();

        $token = $this->issueToken($user, ['trips:read', 'trips:write']);

        $this->withToken($token)
            ->getJson('/api/trips/'.$trip->id.'/timeline')
            ->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta']);

        $created = $this->withToken($token)
            ->postJson('/api/trips/'.$trip->id.'/timeline', [
                'title' => 'First night',
                'description' => 'Checked in',
                'occurred_at' => Carbon::parse('2025-01-12 21:30:00')->toIso8601String(),
                'type' => 'note',
                'location_name' => 'Hotel',
                'tags' => ['arrival'],
                'metadata' => ['mood' => 'happy'],
            ])
            ->assertCreated()
            ->json('data');

        $entryId = $created['id'];

        $this->withToken($token)
            ->getJson('/api/timeline/'.$entryId)
            ->assertOk()
            ->assertJsonPath('data.title', 'First night');

        $this->withToken($token)
            ->putJson('/api/timeline/'.$entryId, [
                'title' => 'First night (updated)',
                'tags' => ['arrival', 'rest'],
            ])
            ->assertOk()
            ->assertJsonPath('data.title', 'First night (updated)')
            ->assertJsonPath('data.tags.1', 'rest');

        $this->assertDatabaseHas('trip_timelines', [
            'id' => $entryId,
            'trip_id' => $trip->id,
            'user_id' => $user->id,
            'title' => 'First night (updated)',
        ]);

        $this->withToken($token)
            ->deleteJson('/api/timeline/'.$entryId)
            ->assertNoContent();

        $this->assertSoftDeleted('trip_timelines', [
            'id' => $entryId,
        ]);
    }

    public function test_timeline_entry_requires_owner(): void
    {
        $owner = User::factory()->create(['password' => 'password']);
        $other = User::factory()->create(['password' => 'password']);

        $trip = Trip::factory()->for($owner)->create();
        $entry = TripTimeline::factory()->for($trip)->create(['user_id' => $owner->id]);

        $token = $this->issueToken($other, ['trips:read']);

        $this->withToken($token)
            ->getJson('/api/timeline/'.$entry->id)
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
