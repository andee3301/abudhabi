<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Trips\TripTimelineBoard;
use App\Models\ItineraryItem;
use App\Models\Trip;
use App\Models\TripEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TripTimelineBoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_backfills_events_from_itinerary_items_when_empty(): void
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->for($user)->create();

        ItineraryItem::factory()->for($trip)->create([
            'type' => 'housing',
            'title' => 'Hotel night',
            'start_datetime' => now()->addDay(),
            'end_datetime' => now()->addDay()->addHours(8),
            'sort_order' => 1,
        ]);

        ItineraryItem::factory()->for($trip)->create([
            'type' => 'transport',
            'title' => 'Flight',
            'start_datetime' => now()->addDays(2),
            'end_datetime' => now()->addDays(2)->addHours(3),
            'sort_order' => 2,
        ]);

        $this->assertDatabaseCount('trip_events', 0);

        Livewire::test(TripTimelineBoard::class, ['trip' => $trip])
            ->assertCount('events', 2)
            ->assertSet('events.0.type', 'hotel')
            ->assertSet('events.0.title', 'Hotel night')
            ->assertSet('events.1.type', 'travel')
            ->assertSet('events.1.title', 'Flight');

        $this->assertDatabaseCount('trip_events', 2);
    }

    public function test_user_can_add_and_edit_event(): void
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->for($user)->create();

        $component = Livewire::test(TripTimelineBoard::class, ['trip' => $trip])
            ->set('type', 'note')
            ->set('title', 'First note')
            ->set('description', 'Hello')
            ->call('save')
            ->assertDispatched('timeline-saved');

        $event = TripEvent::query()->where('trip_id', $trip->id)->firstOrFail();
        $this->assertSame('First note', $event->title);

        $component
            ->call('startEditing', $event->id)
            ->set('title', 'Updated title')
            ->call('save')
            ->assertDispatched('timeline-saved');

        $this->assertDatabaseHas('trip_events', [
            'id' => $event->id,
            'trip_id' => $trip->id,
            'title' => 'Updated title',
        ]);
    }

    public function test_user_can_reorder_and_delete_events(): void
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->for($user)->create();

        $a = TripEvent::factory()->for($trip)->create(['position' => 1, 'title' => 'A']);
        $b = TripEvent::factory()->for($trip)->create(['position' => 2, 'title' => 'B']);
        $c = TripEvent::factory()->for($trip)->create(['position' => 3, 'title' => 'C']);

        Livewire::test(TripTimelineBoard::class, ['trip' => $trip])
            ->call('reorder', [$c->id, $b->id, $a->id]);

        $this->assertDatabaseHas('trip_events', ['id' => $c->id, 'position' => 1]);
        $this->assertDatabaseHas('trip_events', ['id' => $b->id, 'position' => 2]);
        $this->assertDatabaseHas('trip_events', ['id' => $a->id, 'position' => 3]);

        Livewire::test(TripTimelineBoard::class, ['trip' => $trip])
            ->call('deleteEvent', $b->id);

        $this->assertDatabaseMissing('trip_events', ['id' => $b->id]);
    }
}
