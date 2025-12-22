<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTokenControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_credentials_are_rejected(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $this->postJson('/api/auth/token', [
            'email' => $user->email,
            'password' => 'wrong-password',
            'device_name' => 'phpunit',
        ])->assertUnprocessable();
    }

    public function test_invalid_ability_is_rejected(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $this->postJson('/api/auth/token', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'phpunit',
            'abilities' => ['not-a-real-ability'],
        ])->assertUnprocessable();
    }
}
