<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_and_update_profile_with_abilities(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $token = $this->issueToken($user, ['profile:read', 'profile:write']);

        $this->withToken($token)
            ->getJson('/api/profile')
            ->assertOk()
            ->assertJsonPath('email', $user->email);

        $this->withToken($token)
            ->putJson('/api/profile', [
                'name' => 'New Name',
                'timezone' => 'Europe/Lisbon',
                'currency' => 'USD',
            ])
            ->assertOk()
            ->assertJsonPath('name', 'New Name')
            ->assertJsonPath('timezone', 'Europe/Lisbon')
            ->assertJsonPath('currency', 'USD');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
        ]);

        $this->assertDatabaseHas('user_home_settings', [
            'user_id' => $user->id,
            'home_timezone' => 'Europe/Lisbon',
            'preferred_currency' => 'USD',
        ]);
    }

    public function test_can_upload_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['password' => 'password']);
        $token = $this->issueToken($user, ['profile:write']);

        $this->withToken($token)
            ->postJson('/api/profile/avatar', [
                'avatar' => UploadedFile::fake()->image('avatar.jpg', 200, 200),
            ])
            ->assertOk()
            ->assertJsonStructure(['avatar_url']);

        $user->refresh();
        $this->assertNotNull($user->avatar_url);
        $this->assertCount(1, Storage::disk('public')->allFiles('avatars'));
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
