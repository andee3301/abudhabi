<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function __invoke(Request $request)
    {
        $destination = $request->input('q', 'Lisbon, Portugal');

        $info = [
            'summary' => 'Lisbon is a coastal city known for its hills, trams, and pastel de nata.',
            'prep' => ['Visa: Not required for many countries', 'Currency: EUR', 'Weather: Mild, pack layers'],
            'weather' => 'Sunny · 18°C (placeholder)',
        ];

        // TODO: Replace this mocked data with real API response.

        return view('explore.index', compact('destination', 'info'));
    }
}
