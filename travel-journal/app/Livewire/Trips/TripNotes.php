<?php

namespace App\Livewire\Trips;

use App\Models\Trip;
use App\Models\TripNote;
use Livewire\Component;

class TripNotes extends Component
{
    public Trip $trip;

    public ?int $editingNoteId = null;

    public string $title = '';

    public string $body = '';

    public ?string $note_date = '';

    public bool $is_pinned = false;

    public string $tags = '';

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'note_date' => ['nullable', 'date'],
            'is_pinned' => ['boolean'],
            'tags' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function mount(Trip $trip): void
    {
        abort_unless($trip->user_id === auth()->id(), 403);
        $this->trip = $trip;
    }

    public function saveNote(): void
    {
        $payload = $this->buildPayload();

        if ($this->editingNoteId) {
            $note = $this->findNote($this->editingNoteId);
            $note->update($payload);
            session()->flash('status', 'Note updated.');
        } else {
            $payload['trip_id'] = $this->trip->id;
            $payload['user_id'] = auth()->id();
            TripNote::create($payload);
            session()->flash('status', 'Note added.');
        }

        $this->resetForm();
        $this->dispatch('note-saved');
    }

    public function startEditing(int $noteId): void
    {
        $note = $this->findNote($noteId);
        $this->editingNoteId = $note->id;
        $this->title = $note->title;
        $this->body = $note->body;
        $this->note_date = $this->formatForInput($note->note_date);
        $this->is_pinned = (bool) $note->is_pinned;
        $this->tags = implode(', ', $note->tags ?? []);
        $this->resetErrorBag();
    }

    public function cancelEditing(): void
    {
        $this->resetForm();
    }

    public function deleteNote(int $noteId): void
    {
        $note = $this->findNote($noteId);
        $note->delete();

        if ($this->editingNoteId === $noteId) {
            $this->resetForm();
        }

        session()->flash('status', 'Note removed.');
    }

    public function render()
    {
        $notes = $this->trip->tripNotes()
            ->orderByDesc('is_pinned')
            ->orderByDesc('note_date')
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.trips.trip-notes', [
            'notes' => $notes,
        ]);
    }

    protected function buildPayload(): array
    {
        $validated = $this->validate();

        return [
            'title' => $validated['title'],
            'body' => $validated['body'],
            'note_date' => $validated['note_date'] ?: null,
            'is_pinned' => $validated['is_pinned'] ?? false,
            'tags' => $this->parseTags($validated['tags'] ?? ''),
        ];
    }

    protected function resetForm(): void
    {
        $this->reset(['editingNoteId', 'title', 'body', 'note_date', 'is_pinned', 'tags']);
        $this->resetErrorBag();
    }

    protected function findNote(int $noteId): TripNote
    {
        return $this->trip->tripNotes()->where('user_id', auth()->id())->findOrFail($noteId);
    }

    protected function parseTags(string $input): array
    {
        return collect(preg_split('/[,\\n]+/', $input))
            ->map(fn ($tag) => trim($tag))
            ->filter()
            ->values()
            ->all();
    }

    protected function formatForInput($value): ?string
    {
        return $value ? $value->format('Y-m-d') : null;
    }
}
