<?php

namespace Tests\Unit;

use App\Http\Resources\CountryVisitResource;
use App\Http\Resources\MediaResource;
use App\Http\Resources\TripNoteResource;
use App\Models\CountryVisit;
use App\Models\Media;
use App\Models\Region;
use App\Models\Trip;
use App\Models\TripNote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResourceCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_media_resource_includes_url(): void
    {
        Storage::fake('public');

        $media = Media::factory()->create([
            'disk' => 'public',
            'path' => 'journal/example.jpg',
        ]);

        $payload = (new MediaResource($media))->toArray(request());

        $this->assertEquals($media->id, $payload['id']);
        $this->assertEquals('public', $payload['disk']);
        $this->assertEquals('journal/example.jpg', $payload['path']);
        $this->assertStringContainsString('journal/example.jpg', $payload['url']);
    }

    public function test_trip_note_resource_serializes_fields(): void
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->for($user)->create();

        $note = TripNote::create([
            'trip_id' => $trip->id,
            'user_id' => $user->id,
            'title' => 'Hello',
            'body' => 'World',
            'note_date' => '2025-01-01',
            'is_pinned' => true,
            'tags' => ['x'],
            'metadata' => ['a' => 'b'],
        ]);

        $payload = (new TripNoteResource($note))->toArray(request());

        $this->assertEquals($note->id, $payload['id']);
        $this->assertEquals($trip->id, $payload['trip_id']);
        $this->assertEquals($user->id, $payload['user_id']);
        $this->assertEquals('2025-01-01', $payload['note_date']);
        $this->assertTrue($payload['is_pinned']);
        $this->assertEquals(['x'], $payload['tags']);
        $this->assertEquals(['a' => 'b'], $payload['metadata']);
    }

    public function test_country_visit_resource_includes_region_when_loaded(): void
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->for($user)->create();
        $region = Region::factory()->create();

        $visit = CountryVisit::factory()->for($trip)->create([
            'region_id' => $region->id,
        ]);

        $visit->load('region');

        $responseData = (new CountryVisitResource($visit))->response()->getData(true);
        $payload = $responseData['data'];

        $this->assertEquals($visit->id, $payload['id']);
        $this->assertEquals($trip->id, $payload['trip_id']);
        $this->assertEquals($region->id, $payload['region_id']);
        $this->assertIsArray($payload['region']);
        $this->assertEquals($region->id, $payload['region']['id']);
    }
}
