<?php

namespace Tests\Feature\Api;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
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
            'city' => 'Kyoto',
            'country_code' => 'JP',
            'timezone' => 'Asia/Tokyo',
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

    public function test_trips_write_requires_correct_ability(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $tokenResponse = $this->postJson('/api/auth/token', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'limited',
            'abilities' => ['trips:read'],
        ]);

        $this->assertEquals(['trips:read'], $tokenResponse->json('abilities'));

        $token = $tokenResponse->json('token');
        $tokenModel = PersonalAccessToken::findToken($token);
        $this->assertNotNull($tokenModel);
        $this->assertEquals(['trips:read'], $tokenModel->abilities);
        $this->assertFalse($tokenModel->can('trips:write'));

        $payload = [
            'title' => 'Ability Check',
            'primary_location_name' => 'Porto, PT',
            'city' => 'Porto',
            'country_code' => 'PT',
            'timezone' => 'Europe/Lisbon',
            'start_date' => now()->addWeek()->toDateString(),
            'end_date' => now()->addWeeks(2)->toDateString(),
            'status' => 'planned',
        ];

        $this->withToken($token)
            ->postJson('/api/trips', $payload)
            ->assertForbidden();
    }
}
