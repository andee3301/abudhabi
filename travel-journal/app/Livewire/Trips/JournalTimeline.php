<?php

namespace App\Livewire\Trips;

use App\Models\JournalEntry;
use App\Models\Media;
use App\Models\Trip;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class JournalTimeline extends Component
{
    use WithFileUploads;

    public Trip $trip;

    public ?string $entryTitle = '';

    public ?string $body = '';

    public ?string $location = '';

    public bool $is_public = false;

    public array $photos = [];

    protected function rules(): array
    {
        return [
            'entryTitle' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'is_public' => ['boolean'],
            'photos.*' => ['image', 'max:5120'], // 5MB per photo
        ];
    }

    public function mount(Trip $trip): void
    {
        abort_unless($trip->user_id === Auth::id(), 403);
        $this->trip = $trip;
    }

    public function saveEntry(): void
    {
        $validated = $this->validate();

        $now = now($this->trip->timezone ?? config('app.timezone'));

        $entry = JournalEntry::create([
            'trip_id' => $this->trip->id,
            'user_id' => Auth::id(),
            'entry_date' => $now->toDateString(),
            'title' => filled($validated['entryTitle'] ?? null) ? $validated['entryTitle'] : 'Journal entry',
            'body' => $validated['body'],
        ]);

        foreach ($this->photos as $upload) {
            $path = $upload->store('journal/'.$this->trip->id, 'public');

            Media::create([
                'journal_entry_id' => $entry->id,
                'disk' => 'public',
                'path' => $path,
                'mime_type' => $upload->getMimeType(),
                'size' => $upload->getSize(),
            ]);
        }

        $this->reset(['entryTitle', 'body', 'location', 'is_public', 'photos']);
        $this->dispatch('entryCreated');
        session()->flash('status', 'Journal entry saved.');
    }

    public function render()
    {
        $entries = $this->trip->journalEntries()
            ->with(['media', 'user'])
            ->latest('logged_at')
            ->get();

        return view('livewire.trips.journal-timeline', [
            'entries' => $entries,
        ]);
    }
}
