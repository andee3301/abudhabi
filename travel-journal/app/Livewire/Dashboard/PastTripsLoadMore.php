<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class PastTripsLoadMore extends Component
{
    public bool $disabled = false;

    public function render()
    {
        return view('livewire.dashboard.past-trips-load-more');
    }

    public function load(): void
    {
        if ($this->disabled) {
            return;
        }

        $this->dispatch('pastTrips:load-more');
    }
}
