<?php

namespace Tests\Feature\Jobs;

use App\Jobs\AggregateUserStats;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AggregateUserStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_can_be_dispatched(): void
    {
        Queue::fake();

        $user = User::factory()->create();

        AggregateUserStats::dispatch($user->id);

        Queue::assertPushed(AggregateUserStats::class, function ($job) use ($user) {
            return $job->userId === $user->id;
        });
    }

    public function test_job_aggregates_user_stats(): void
    {
        $user = User::factory()
            ->hasTrips(['status' => 'completed'])
            ->hasTrips(['status' => 'ongoing'])
            ->create();

        $job = new AggregateUserStats($user->id);
        $job->handle();

        $cached = Cache::get("user:{$user->id}:stats");

        $this->assertNotNull($cached);
        $this->assertIsArray($cached);
        $this->assertArrayHasKey('total_trips', $cached);
        $this->assertArrayHasKey('completed_trips', $cached);
        $this->assertEquals(2, $cached['total_trips']);
    }
}
