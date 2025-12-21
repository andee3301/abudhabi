<?php

namespace Tests\Feature;

use App\Models\Trip;
use App\Models\TripNote;
use App\Models\User;
use App\Support\TripCache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class TripCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_trip_cache_is_invalidated_on_note_create(): void
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->for($user)->create();

        // Populate cache
        Cache::tags(['trips', "user:{$user->id}"])->put('test-key', 'test-value', 60);

        // Create note which should flush cache
        TripNote::create([
            'trip_id' => $trip->id,
            'user_id' => $user->id,
            'title' => 'Test Note',
            'body' => 'Test Body',
        ]);

        // Cache should be flushed
        $this->assertNull(Cache::tags(['trips', "user:{$user->id}"])->get('test-key'));
    }

    public function test_trip_cache_helper_generates_correct_keys(): void
    {
        $key = TripCache::listKey(1, 'completed', 'search term');

        $this->assertStringContainsString('user:1', $key);
        $this->assertStringContainsString('status:completed', $key);
        $this->assertStringContainsString('search:', $key);
    }

    public function test_trip_cache_helper_remembers_with_tags(): void
    {
        $user = User::factory()->create();

        $result = TripCache::rememberList($user->id, null, null, function () {
            return 'cached-data';
        }, 60);

        $this->assertEquals('cached-data', $result);

        // Verify it's cached with tags
        $cached = Cache::tags(['trips', "user:{$user->id}"])->get(TripCache::listKey($user->id, null, null));
        $this->assertEquals('cached-data', $cached);
    }
}
