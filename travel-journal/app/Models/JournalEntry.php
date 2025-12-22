<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    /** @use HasFactory<\Database\Factories\JournalEntryFactory> */
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'user_id',
        'title',
        'body',
        'entry_date',
        'mood',
        'photo_urls',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'photo_urls' => 'array',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
