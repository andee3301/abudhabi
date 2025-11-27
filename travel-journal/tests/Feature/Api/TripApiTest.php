<?php

namespace Tests\Feature\Api;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
            'destination' => 'Kyoto, Japan',
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
        Storage::fake('public');

        $user = User::factory()->create(['password' => 'password']);
        $trip = Trip::factory()->for($user)->create([
            'start_date' => now(),
            'end_date' => now()->addWeek(),
            'status' => 'in_progress',
        ]);

        $token = $this->postJson('/api/auth/token', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'phpunit',
        ])->json('token');

        $photo = UploadedFile::fake()->image('photo.jpg');

        $this->withToken($token)
            ->post('/api/trips/'.$trip->id.'/entries', [
                'body' => 'Visited Fushimi Inari today.',
                'is_public' => true,
                'photos' => [$photo],
            ], ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('data.body', 'Visited Fushimi Inari today.');

        Storage::disk('public')->assertExists('journal/'.$trip->id);
    }
}
