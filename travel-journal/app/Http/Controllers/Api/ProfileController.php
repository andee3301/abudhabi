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
        return $request->user();
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users')->ignore($request->user()->id)],
            'timezone' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'max:10'],
        ]);

        $request->user()->update($data);

        return $request->user()->fresh();
    }

    public function avatar(Request $request)
    {
        $data = $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $path = $request->file('avatar')->store('avatars', 'public');

        $request->user()->forceFill([
            'avatar_path' => $path,
        ])->save();

        return response()->json([
            'avatar_url' => Storage::disk('public')->url($path),
        ]);
    }
}
