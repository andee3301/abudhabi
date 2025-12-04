<?php

namespace Tests\Feature\Api;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_issue_token_and_manage_trips(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $token = $this->postJson('/api/auth/token', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'phpunit',
        ])->assertOk()->json('token');

        $payload = [
            'title' => 'Weekend in Kyoto',
            'primary_location_name' => 'Kyoto, Japan',
            'start_date' => now()->addWeek()->toDateString(),
            'end_date' => now()->addWeeks(2)->toDateString(),
            'status' => 'planned',
        ];

        $this->withToken($token)
            ->postJson('/api/trips', $payload)
            ->assertCreated()
            ->assertJsonPath('data.title', $payload['title']);

        $this->withToken($token)
            ->getJson('/api/trips')
            ->assertOk()
            ->assertJsonFragment(['title' => $payload['title']]);
    }

    public function test_can_create_journal_entry_with_media(): void
    {
        $user = User::factory()->create(['password' => 'password']);
        $trip = Trip::factory()->for($user)->create([
            'start_date' => now(),
            'end_date' => now()->addWeek(),
            'status' => 'ongoing',
        ]);

        $token = $this->postJson('/api/auth/token', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'phpunit',
        ])->json('token');

        $this->withToken($token)
            ->postJson('/api/trips/'.$trip->id.'/journal', [
                'title' => 'Visited Fushimi Inari',
                'entry_date' => now()->toDateString(),
                'body' => 'Visited Fushimi Inari today.',
                'mood' => 'joyful',
                'photo_urls' => ['https://example.com/photo.jpg'],
            ], ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('data.body', 'Visited Fushimi Inari today.');
    }
}
