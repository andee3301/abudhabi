<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Trips\JournalTimeline;
use App\Models\JournalEntry;
use App\Models\Media;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class JournalTimelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_journal_entry_with_photo(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $trip = Trip::factory()->for($user)->create([
            'timezone' => 'UTC',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
        ]);

        $this->actingAs($user);

        $photo = UploadedFile::fake()->image('photo.jpg');

        Livewire::test(JournalTimeline::class, ['trip' => $trip])
            ->set('entryTitle', 'Day 1')
            ->set('body', 'Hello from the road')
            ->set('location', 'Hotel')
            ->set('is_public', true)
            ->set('photos', [$photo])
            ->call('saveEntry')
            ->assertDispatched('entryCreated');

        $entry = JournalEntry::query()->where('trip_id', $trip->id)->firstOrFail();

        $this->assertSame('Day 1', $entry->title);
        $this->assertSame('Hello from the road', $entry->body);

        $media = Media::query()->where('journal_entry_id', $entry->id)->firstOrFail();

        $this->assertSame('public', $media->disk);
        $this->assertTrue(str_starts_with($media->path, 'journal/'.$trip->id.'/'));
        $this->assertTrue(Storage::disk('public')->exists($media->path));
    }
}
