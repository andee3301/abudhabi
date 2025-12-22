<?php

namespace Tests\Feature\Web;

use App\Models\JournalEntry;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_journal_create_can_preselect_trip(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $trip = Trip::factory()->for($user)->create();

        $this->actingAs($user)
            ->get('/journal/create?trip_id='.$trip->id)
            ->assertOk()
            ->assertViewIs('journal.form')
            ->assertViewHas('trip', fn ($t) => $t?->id === $trip->id);
    }

    public function test_user_can_create_update_and_delete_journal_entries(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $trip = Trip::factory()->for($user)->create();

        $this->actingAs($user)
            ->post('/journal', [
                'trip_id' => $trip->id,
                'title' => 'Day 1',
                'date' => now()->toDateString(),
                'mood' => 'joyful',
                'content' => 'Hello world',
                'photo_urls' => ['https://example.com/a.jpg'],
            ])
            ->assertRedirect('/trips/'.$trip->id)
            ->assertSessionHas('status', 'Journal entry saved.');

        $entry = JournalEntry::query()->where('trip_id', $trip->id)->where('title', 'Day 1')->firstOrFail();
        $this->assertEquals('Hello world', $entry->body);

        $this->actingAs($user)
            ->get('/journal/'.$entry->id.'/edit')
            ->assertOk()
            ->assertViewIs('journal.form')
            ->assertViewHas('entry');

        $this->actingAs($user)
            ->put('/journal/'.$entry->id, [
                'trip_id' => $trip->id,
                'title' => 'Day 1 updated',
                'date' => now()->toDateString(),
                'mood' => null,
                'content' => 'Updated body',
                'photo_urls' => [],
            ])
            ->assertRedirect('/trips/'.$trip->id)
            ->assertSessionHas('status', 'Journal entry updated.');

        $this->assertDatabaseHas('journal_entries', [
            'id' => $entry->id,
            'title' => 'Day 1 updated',
            'body' => 'Updated body',
        ]);

        $this->actingAs($user)
            ->delete('/journal/'.$entry->id)
            ->assertRedirect('/trips/'.$trip->id)
            ->assertSessionHas('status', 'Journal entry deleted.');

        $this->assertDatabaseMissing('journal_entries', [
            'id' => $entry->id,
        ]);
    }

    public function test_journal_entry_actions_forbid_non_owner(): void
    {
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $other = User::factory()->create(['email_verified_at' => now()]);

        $trip = Trip::factory()->for($owner)->create();
        $entry = JournalEntry::factory()->for($trip)->create();

        $this->actingAs($other)
            ->get('/journal/'.$entry->id.'/edit')
            ->assertForbidden();

        $this->actingAs($other)
            ->put('/journal/'.$entry->id, [
                'trip_id' => $trip->id,
                'title' => 'Nope',
                'date' => now()->toDateString(),
                'content' => 'Nope',
            ])
            ->assertForbidden();

        $this->actingAs($other)
            ->delete('/journal/'.$entry->id)
            ->assertForbidden();
    }
}
