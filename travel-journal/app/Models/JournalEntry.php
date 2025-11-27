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
        'weather_snapshot_id',
        'title',
        'body',
        'location',
        'logged_at',
        'is_public',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
        'is_public' => 'boolean',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function weatherSnapshot()
    {
        return $this->belongsTo(WeatherSnapshot::class);
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }
}
