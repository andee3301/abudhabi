<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJournalEntryRequest;
use App\Models\Trip;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function create(Request $request)
    {
        $tripId = $request->input('trip_id');
        $trip = $tripId ? Trip::whereBelongsTo($request->user())->findOrFail($tripId) : null;

        return view('journal.form', [
            'trip' => $trip,
        ]);
    }

    public function store(StoreJournalEntryRequest $request)
    {
        $trip = Trip::whereBelongsTo($request->user())->findOrFail($request->trip_id);

        $trip->journalEntries()->create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'body' => $request->content,
            'entry_date' => $request->date,
            'mood' => $request->mood,
            'photo_urls' => $request->photo_urls ?? [],
        ]);

        return redirect()->route('trips.show', $trip)->with('status', 'Journal entry saved.');
    }
}
