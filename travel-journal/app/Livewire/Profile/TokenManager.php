<?php

namespace App\Livewire\Profile;

use Livewire\Attributes\Validate;
use Livewire\Component;

class TokenManager extends Component
{
    protected array $allowedAbilities = [
        'trips:read',
        'trips:write',
        'itinerary:read',
        'itinerary:write',
        'journal:read',
        'journal:write',
        'events:read',
        'events:write',
        'profile:read',
        'profile:write',
    ];

    #[Validate('required|string|min:3|max:255')]
    public string $tokenName = 'API Token';

    public array $selected = [];

    public ?string $plainToken = null;

    public function mount(): void
    {
        $this->selected = $this->allowedAbilities;
    }

    public function toggleAbility(string $ability): void
    {
        if (! in_array($ability, $this->allowedAbilities, true)) {
            return;
        }

        if (in_array($ability, $this->selected, true)) {
            $this->selected = array_values(array_diff($this->selected, [$ability]));
        } else {
            $this->selected[] = $ability;
        }
    }

    public function create(): void
    {
        $this->validate();

        $abilities = $this->selected ?: $this->allowedAbilities;
        $user = auth()->user();

        $token = $user->createToken($this->tokenName, $abilities);
        $this->plainToken = $token->plainTextToken;

        $this->dispatch('token-created');
    }

    public function revoke(int $tokenId): void
    {
        auth()->user()->tokens()->where('id', $tokenId)->delete();
        $this->dispatch('token-revoked');
    }

    public function render()
    {
        return view('livewire.profile.token-manager', [
            'tokens' => auth()->user()->tokens()->latest()->get(),
            'allowed' => $this->allowedAbilities,
        ]);
    }
}
