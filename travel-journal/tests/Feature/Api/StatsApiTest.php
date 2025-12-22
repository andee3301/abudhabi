<?php

namespace Tests\Feature\Api;

use App\Models\CountryVisit;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StatsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_overview_returns_trip_and_country_counts(): void
    {
        Carbon::setTestNow(Carbon::parse('2025-06-01'));

        $user = User::factory()->create(['password' => 'password']);

        // Two trips this year, one last year.
        $trip2025a = Trip::factory()->for($user)->create(['start_date' => '2025-01-01']);
        $trip2025b = Trip::factory()->for($user)->create(['start_date' => '2025-02-01']);
        $trip2024 = Trip::factory()->for($user)->create(['start_date' => '2024-02-01']);

        CountryVisit::factory()->for($trip2025a)->create(['country_code' => 'AE']);
        CountryVisit::factory()->for($trip2025b)->create(['country_code' => 'AE']);
        CountryVisit::factory()->for($trip2024)->create(['country_code' => 'FR']);

        $token = $this->issueToken($user, ['stats:read']);

        $payload = $this->withToken($token)
            ->getJson('/api/stats/overview')
            ->assertOk()
            ->json();

        $this->assertSame(2, $payload['trips_this_year']);
        $this->assertSame(3, $payload['total_trips']);
        $this->assertSame(2, $payload['countries_visited']);

        // AE should be top with 2.
        $this->assertSame('AE', $payload['countries'][0]['country_code']);
        $this->assertSame(2, (int) $payload['countries'][0]['total']);

        Carbon::setTestNow();
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
