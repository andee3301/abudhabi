<?php

namespace App\Livewire\Trips;

use App\Models\Trip;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ManageTrips extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = 'all';
    public string $title = '';
    public string $primary_location_name = '';
    public string $start_date = '';
    public string $end_date = '';
    public ?string $notes = '';
    public ?string $companion_name = '';

    protected $queryString = ['search', 'status'];

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'primary_location_name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in(['planned', 'ongoing', 'completed', 'all'])],
            'notes' => ['nullable', 'string'],
            'companion_name' => ['nullable', 'string', 'max:255'],
            'search' => ['nullable', 'string'],
        ];
    }

    public function mount(): void
    {
        //
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function createTrip(): void
    {
        $validated = $this->validate();
        $validated['user_id'] = auth()->id();

        $trip = Trip::create($validated);

        $this->reset(['title', 'primary_location_name', 'start_date', 'end_date', 'notes', 'companion_name']);
        $this->resetPage();

        $this->dispatch('tripCreated', id: $trip->id);
        session()->flash('status', 'Trip created successfully.');
    }

    public function render()
    {
        $trips = Trip::withCount('journalEntries')
            ->whereBelongsTo(auth()->user())
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('primary_location_name', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->status !== 'all', function ($query) {
                $query->where('status', $this->status);
            })
            ->orderByDesc('start_date')
            ->paginate(10);

        return view('livewire.trips.manage-trips', [
            'trips' => $trips,
        ]);
    }
}
