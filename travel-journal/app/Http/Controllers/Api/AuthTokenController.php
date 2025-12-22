<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthTokenController extends Controller
{
    protected array $allowedAbilities = [
        'trips:read',
        'trips:write',
        'itinerary:read',
        'itinerary:write',
        'journal:read',
        'journal:write',
        'stats:read',
        'cities:read',
        'events:read',
        'events:write',
        'profile:read',
        'profile:write',
    ];

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string'],
            'abilities' => ['nullable', 'array'],
            'abilities.*' => ['string', Rule::in($this->allowedAbilities)],
        ]);

        /** @var \App\Models\User|null $user */
        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $abilities = $request->has('abilities')
            ? (array) $request->input('abilities')
            : $this->allowedAbilities;

        $token = $user->createToken($credentials['device_name'], $abilities)->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
            'abilities' => $abilities,
        ]);
    }
}
