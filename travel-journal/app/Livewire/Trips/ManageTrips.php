<?php

namespace App\Livewire\Trips;

use App\Models\City;
use App\Models\Region;
use App\Models\Trip;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ManageTrips extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterStatus = 'all';

    public string $formStatus = 'planned';

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

    public ?string $location_overview = '';

    public ?string $companion_name = '';

    public string $cityStopsInput = '';

    public string $wishlistInput = '';

    public array $timezoneOptions = [];

    public $regions;

    public ?int $editingTripId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['as' => 'status', 'except' => 'all'],
    ];

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
            'formStatus' => ['required', Rule::in(['planned', 'ongoing', 'completed'])],
            'notes' => ['nullable', 'string'],
            'location_overview' => ['nullable', 'string', 'max:1000'],
            'cityStopsInput' => ['nullable', 'string', 'max:2000'],
            'wishlistInput' => ['nullable', 'string', 'max:2000'],
            'companion_name' => ['nullable', 'string', 'max:255'],
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
        $this->formStatus = 'planned';
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function createTrip(): void
    {
        $payload = $this->validatedPayload();
        $payload['user_id'] = auth()->id();

        if ($payload['region_id'] ?? null) {
            $region = $this->regions->firstWhere('id', $payload['region_id']);
            if ($region) {
                $payload['state_region'] = $payload['state_region'] ?: $region->name;
                $payload['country_code'] = $payload['country_code'] ?: $region->country_code;
                $payload['timezone'] = $payload['timezone'] ?: ($region->default_timezone ?? 'UTC');
            }
        }

        $this->ensureSingleOngoing();

        $trip = Trip::create($payload);

        $this->resetForm();

        $this->dispatch('tripCreated', id: $trip->id);
        session()->flash('status', 'Trip created successfully.');
    }

    public function startEditing(int $tripId): void
    {
        $trip = $this->findTrip($tripId);
        $this->editingTripId = $trip->id;

        $this->title = $trip->title;
        $this->primary_location_name = $trip->primary_location_name;
        $this->city = $trip->city;
        $this->state_region = $trip->state_region;
        $this->country_code = $trip->country_code;
        $this->timezone = $trip->timezone;
        $this->region_id = $trip->region_id;
        $this->start_date = optional($trip->start_date)?->format('Y-m-d') ?? '';
        $this->end_date = optional($trip->end_date)?->format('Y-m-d') ?? '';
        $this->notes = $trip->notes;
        $this->location_overview = $trip->location_overview;
        $this->companion_name = $trip->companion_name;
        $this->formStatus = $trip->status;
        $this->cityStopsInput = collect($trip->city_stops ?? [])->pluck('label')->implode(', ');
        $this->wishlistInput = collect($trip->wishlist_locations ?? [])->implode(', ');
    }

    public function cancelEditing(): void
    {
        $this->resetForm();
    }

    public function updateTrip(): void
    {
        if (! $this->editingTripId) {
            return;
        }

        $payload = $this->validatedPayload();
        $trip = $this->findTrip($this->editingTripId);

        $this->ensureSingleOngoing($trip->id);

        $trip->update($payload);

        $this->resetForm();
        $this->resetPage();

        session()->flash('status', 'Trip updated.');
    }

    public function deleteTrip(int $tripId): void
    {
        $trip = $this->findTrip($tripId);
        $trip->delete();

        if ($this->editingTripId === $tripId) {
            $this->resetForm();
        }

        $this->resetPage();
        session()->flash('status', 'Trip removed.');
    }

    public function markOngoing(int $tripId): void
    {
        $trip = $this->findTrip($tripId);
        $this->ensureSingleOngoing($trip->id, true);
        $trip->update(['status' => 'ongoing']);

        session()->flash('status', 'Marked as ongoing. Previous active trips were set to planned.');
        $this->resetPage();
    }

    public function markCompleted(int $tripId): void
    {
        $trip = $this->findTrip($tripId);
        $trip->update(['status' => 'completed']);

        session()->flash('status', 'Trip marked as completed.');
        $this->resetPage();
    }

    protected function validatedPayload(): array
    {
        $validated = $this->validate();
        $validated['status'] = $validated['formStatus'];
        unset($validated['formStatus']);

        $validated['city_stops'] = $this->parseStops($this->cityStopsInput, $validated['country_code']);
        $validated['wishlist_locations'] = $this->parseList($this->wishlistInput);

        if (isset($validated['city_stops'][0]['city_id']) && $validated['city_stops'][0]['city_id']) {
            $validated['city_id'] = $validated['city_stops'][0]['city_id'];
        }

        return $validated;
    }

    protected function resetForm(): void
    {
        $this->reset(['editingTripId', 'title', 'primary_location_name', 'city', 'state_region', 'country_code', 'timezone', 'region_id', 'start_date', 'end_date', 'notes', 'location_overview', 'companion_name', 'cityStopsInput', 'wishlistInput']);
        $this->country_code = 'US';
        $this->timezone = 'UTC';
        $this->formStatus = 'planned';
    }

    protected function ensureSingleOngoing(?int $exceptId = null, bool $force = false): void
    {
        if (! $force && $this->formStatus !== 'ongoing') {
            return;
        }

        Trip::where('user_id', auth()->id())
            ->where('status', 'ongoing')
            ->when($exceptId, fn ($query) => $query->where('id', '!=', $exceptId))
            ->update(['status' => 'planned']);
    }

    protected function findTrip(int $tripId): Trip
    {
        return Trip::whereBelongsTo(auth()->user())->findOrFail($tripId);
    }

    protected function parseStops(string $input, ?string $fallbackCountry): array
    {
        return collect(preg_split('/[,\\n]+/', $input))
            ->map(fn ($piece) => trim($piece))
            ->filter()
            ->map(function ($token) use ($fallbackCountry) {
                // Accept "City (CC)" or "City - CC" or plain "City"
                if (preg_match('/^(.*?)\\s*[\\(\\-]\\s*([A-Za-z]{2})\\)?$/', $token, $matches)) {
                    $name = trim($matches[1]);
                    $country = strtoupper($matches[2]);
                } else {
                    $name = $token;
                    $country = $fallbackCountry ?: 'ZZ';
                }

                $city = City::firstOrCreate(
                    ['name' => $name, 'country_code' => $country],
                    ['slug' => Str::slug($name.'-'.$country)]
                );

                return [
                    'label' => $name,
                    'country_code' => $country,
                    'city_id' => $city->id,
                ];
            })
            ->values()
            ->all();
    }

    protected function parseList(string $input): array
    {
        return collect(preg_split('/[,\\n]+/', $input))
            ->map(fn ($piece) => trim($piece))
            ->filter()
            ->values()
            ->all();
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
            ->when($this->filterStatus !== 'all', function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->orderByRaw("status = 'ongoing' DESC")
            ->orderByDesc('start_date')
            ->paginate(10);

        return view('livewire.trips.manage-trips', [
            'trips' => $trips,
            'regions' => $this->regions,
            'timezones' => $this->timezoneOptions,
        ]);
    }
}
