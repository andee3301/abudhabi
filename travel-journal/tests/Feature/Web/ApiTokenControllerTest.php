<?php

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTokenControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_personal_access_token_from_profile(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->from('/profile')
            ->post('/profile/api-token', [
                'token_name' => '',
            ])
            ->assertRedirect('/profile')
            ->assertSessionHas('token_plain');

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => $user::class,
            'tokenable_id' => $user->id,
            'name' => 'API Token',
        ]);
    }
}
