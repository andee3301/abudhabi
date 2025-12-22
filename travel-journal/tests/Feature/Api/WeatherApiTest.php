<?php

namespace Tests\Feature\Api;

use App\Jobs\FetchWeatherForTrip;
use App\Models\Trip;
use App\Models\User;
use App\Models\WeatherSnapshot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class WeatherApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_weather_snapshots_and_queue_fetch(): void
    {
        Bus::fake();

        $user = User::factory()->create(['password' => 'password']);
        $trip = Trip::factory()->for($user)->create();

        WeatherSnapshot::factory()->for($trip)->create(['recorded_at' => Carbon::parse('2025-01-02 12:00:00')]);
        WeatherSnapshot::factory()->for($trip)->create(['recorded_at' => Carbon::parse('2025-01-03 12:00:00')]);

        $token = $this->issueToken($user, ['trips:read', 'trips:write']);

        $this->withToken($token)
            ->getJson('/api/trips/'.$trip->id.'/weather')
            ->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta'])
            ->assertJsonPath('data.0.recorded_at', '2025-01-03T12:00:00.000000Z');

        $this->withToken($token)
            ->postJson('/api/trips/'.$trip->id.'/weather/fetch')
            ->assertOk()
            ->assertJson(['queued' => true]);

        Bus::assertDispatched(FetchWeatherForTrip::class, function ($job) use ($trip) {
            return $job->trip->is($trip);
        });
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
