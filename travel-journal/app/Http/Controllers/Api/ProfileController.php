<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return $request->user()->loadMissing('homeSettings');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users')->ignore($request->user()->id)],
            'timezone' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'max:10'],
        ]);

        $user = $request->user();

        $user->update(collect($data)->only(['name', 'email'])->all());

        if (array_key_exists('timezone', $data) || array_key_exists('currency', $data)) {
            $user->homeSettings()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'home_timezone' => $data['timezone'] ?? $user->homeSettings?->home_timezone,
                    'preferred_currency' => $data['currency'] ?? $user->homeSettings?->preferred_currency,
                ]
            );
        }

        return $user->fresh()->loadMissing('homeSettings');
    }

    public function avatar(Request $request)
    {
        $data = $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $path = $request->file('avatar')->store('avatars', 'public');

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        $avatarUrl = $disk->url($path);

        $request->user()->forceFill([
            'avatar_url' => $avatarUrl,
        ])->save();

        return response()->json([
            'avatar_url' => $avatarUrl,
        ]);
    }
}
