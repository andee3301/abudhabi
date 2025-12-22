<?php

namespace Tests\Feature\Api;

use App\Models\Trip;
use App\Models\TripNote;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class TripNotesApiTest extends TestCase
{
    use RefreshDatabase;

    private function issueToken(User $user, array $abilities): string
    {
        $response = $this->postJson('/api/auth/token', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'phpunit',
            'abilities' => $abilities,
        ])->assertOk();

        $token = $response->json('token');

        $this->assertNotEmpty($token);
        $tokenModel = PersonalAccessToken::findToken($token);
        $this->assertNotNull($tokenModel);

        return $token;
    }

    public function test_can_crud_trip_notes_and_enforces_ownership(): void
    {
        Carbon::setTestNow(Carbon::parse('2025-01-10 10:00:00'));

        $user = User::factory()->create([
            'password' => 'password',
        ]);
        $otherUser = User::factory()->create([
            'password' => 'password',
        ]);

        $trip = Trip::factory()->for($user)->create();
        $otherTrip = Trip::factory()->for($otherUser)->create();

        $token = $this->issueToken($user, ['trips:read', 'trips:write']);

        $older = TripNote::create([
            'trip_id' => $trip->id,
            'user_id' => $user->id,
            'title' => 'Older note',
            'body' => 'Old body',
            'note_date' => '2025-01-01',
            'is_pinned' => false,
            'tags' => ['a'],
            'metadata' => ['source' => 'seed'],
        ]);

        $newer = TripNote::create([
            'trip_id' => $trip->id,
            'user_id' => $user->id,
            'title' => 'Newer note',
            'body' => 'New body',
            'note_date' => '2025-01-05',
            'is_pinned' => true,
            'tags' => ['b'],
            'metadata' => ['source' => 'seed'],
        ]);

        // Index orders by note_date desc, then created_at desc
        $this->withToken($token)
            ->getJson('/api/trips/'.$trip->id.'/notes')
            ->assertOk()
            ->assertJsonPath('data.0.id', $newer->id)
            ->assertJsonPath('data.1.id', $older->id);

        // Store applies defaults for note_date/tags/metadata
        $this->withToken($token)
            ->postJson('/api/trips/'.$trip->id.'/notes', [
                'title' => 'Created note',
                'body' => 'Created body',
            ])
            ->assertCreated()
            ->assertJsonPath('data.title', 'Created note')
            ->assertJsonPath('data.note_date', '2025-01-10')
            ->assertJsonPath('data.tags', [])
            ->assertJsonPath('data.metadata', []);

        $createdId = TripNote::query()->where('trip_id', $trip->id)->where('title', 'Created note')->value('id');
        $this->assertNotNull($createdId);

        // Show
        $this->withToken($token)
            ->getJson('/api/notes/'.$newer->id)
            ->assertOk()
            ->assertJsonPath('data.id', $newer->id);

        // Update (partial)
        $this->withToken($token)
            ->putJson('/api/notes/'.$newer->id, [
                'title' => 'Updated title',
                'tags' => ['x', 'y'],
            ])
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated title')
            ->assertJsonPath('data.tags.0', 'x');

        $this->assertDatabaseHas('trip_notes', [
            'id' => $newer->id,
            'title' => 'Updated title',
        ]);

        // Destroy (soft delete)
        $this->withToken($token)
            ->deleteJson('/api/notes/'.$older->id)
            ->assertNoContent();

        $this->assertSoftDeleted('trip_notes', ['id' => $older->id]);

        // Ownership checks
        $foreignNote = TripNote::create([
            'trip_id' => $otherTrip->id,
            'user_id' => $otherUser->id,
            'title' => 'Foreign note',
            'body' => 'Foreign body',
            'note_date' => '2025-01-02',
            'is_pinned' => false,
            'tags' => [],
            'metadata' => [],
        ]);

        $this->withToken($token)
            ->getJson('/api/trips/'.$otherTrip->id.'/notes')
            ->assertForbidden();

        $this->withToken($token)
            ->getJson('/api/notes/'.$foreignNote->id)
            ->assertForbidden();

        $this->withToken($token)
            ->putJson('/api/notes/'.$foreignNote->id, ['title' => 'Nope'])
            ->assertForbidden();

        $this->withToken($token)
            ->deleteJson('/api/notes/'.$foreignNote->id)
            ->assertForbidden();
    }

    public function test_trips_write_ability_is_required_for_mutations(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);
        $trip = Trip::factory()->for($user)->create();

        $token = $this->issueToken($user, ['trips:read']);

        $this->withToken($token)
            ->postJson('/api/trips/'.$trip->id.'/notes', [
                'title' => 'No write',
                'body' => 'No write',
            ])
            ->assertForbidden();

        $note = TripNote::create([
            'trip_id' => $trip->id,
            'user_id' => $user->id,
            'title' => 'Note',
            'body' => 'Body',
            'note_date' => '2025-01-01',
            'is_pinned' => false,
            'tags' => [],
            'metadata' => [],
        ]);

        $this->withToken($token)
            ->putJson('/api/notes/'.$note->id, ['title' => 'Still no write'])
            ->assertForbidden();

        $this->withToken($token)
            ->deleteJson('/api/notes/'.$note->id)
            ->assertForbidden();
    }
}
