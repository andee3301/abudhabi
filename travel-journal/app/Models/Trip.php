<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    /** @use HasFactory<\Database\Factories\TripFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'destination',
        'start_date',
        'end_date',
        'status',
        'notes',
        'cover_image_path',
        'timezone',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function media()
    {
        return $this->hasManyThrough(Media::class, JournalEntry::class);
    }

    public function weatherSnapshots()
    {
        return $this->hasMany(WeatherSnapshot::class);
    }

    public function latestWeather()
    {
        return $this->hasOne(WeatherSnapshot::class)->latestOfMany();
    }
}
