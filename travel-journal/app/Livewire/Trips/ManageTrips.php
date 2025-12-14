<?php

namespace App\Livewire\Trips;

use App\Models\Region;
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
    public ?string $city = '';
    public ?string $state_region = '';
    public ?string $country_code = '';
    public ?string $timezone = '';
    public ?int $region_id = null;
    public string $start_date = '';
    public string $end_date = '';
    public ?string $notes = '';
    public ?string $companion_name = '';
    public array $timezoneOptions = [];
    public $regions;

    protected $queryString = ['search', 'status'];

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'primary_location_name' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state_region' => ['nullable', 'string', 'max:255'],
            'country_code' => ['required', 'string', 'size:2'],
            'timezone' => ['required', 'string', 'max:64'],
            'region_id' => ['nullable', 'integer', Rule::exists('regions', 'id')],
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
        $this->timezoneOptions = collect(['UTC'])
            ->concat(
                collect(\DateTimeZone::listIdentifiers())
                    ->filter(fn ($tz) => str_contains($tz, '/'))
                    ->take(120)
            )
            ->unique()
            ->values()
            ->all();

        $this->regions = Region::orderBy('country_code')->orderBy('name')->get();

        $this->country_code = 'US';
        $this->timezone = 'UTC';
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

        if ($validated['region_id'] ?? null) {
            $region = $this->regions->firstWhere('id', $validated['region_id']);
            if ($region) {
                $validated['state_region'] = $validated['state_region'] ?: $region->name;
                $validated['country_code'] = $validated['country_code'] ?: $region->country_code;
                $validated['timezone'] = $validated['timezone'] ?: ($region->default_timezone ?? 'UTC');
            }
        }

        $trip = Trip::create($validated);

        $this->reset(['title', 'primary_location_name', 'city', 'state_region', 'country_code', 'timezone', 'region_id', 'start_date', 'end_date', 'notes', 'companion_name']);
        $this->country_code = 'US';
        $this->timezone = 'UTC';
        $this->resetPage();

        $this->dispatch('tripCreated', id: $trip->id);
        session()->flash('status', 'Trip created successfully.');
    }

    public function render()
    {
        $trips = Trip::with(['region'])
            ->withCount('journalEntries')
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
            'regions' => $this->regions,
            'timezones' => $this->timezoneOptions,
        ]);
    }
}
