<?php

namespace Tests\Unit;

use App\Models\CountryVisit;
use App\Models\ItineraryItem;
use App\Models\JournalEntry;
use App\Models\Media;
use App\Models\Region;
use App\Models\Trip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationsCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_region_relations_work(): void
    {
        $region = Region::factory()->create();

        $trip = Trip::factory()->create(['region_id' => $region->id]);
        $this->assertTrue($region->trips()->whereKey($trip->id)->exists());

        $item = ItineraryItem::factory()->create([
            'trip_id' => $trip->id,
            'region_id' => $region->id,
        ]);
        $this->assertTrue($region->itineraryItems()->whereKey($item->id)->exists());

        $visit = CountryVisit::factory()->create([
            'trip_id' => $trip->id,
            'region_id' => $region->id,
        ]);
        $this->assertTrue($region->countryVisits()->whereKey($visit->id)->exists());
    }

    public function test_media_belongs_to_journal_entry(): void
    {
        $entry = JournalEntry::factory()->create();
        $media = Media::factory()->create(['journal_entry_id' => $entry->id]);

        $this->assertSame($entry->id, $media->journalEntry->id);
        $this->assertSame($media->id, $entry->media()->firstOrFail()->id);
    }
}
