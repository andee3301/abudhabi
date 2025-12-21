<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'token_name' => ['nullable', 'string', 'max:255'],
        ]);

        $name = $validated['token_name'] ?: 'API Token';

        $plainTextToken = $request->user()
            ->createToken($name, ['*'])
            ->plainTextToken;

        return back()->with('token_plain', $plainTextToken);
    }
}
