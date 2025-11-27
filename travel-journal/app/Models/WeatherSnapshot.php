<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherSnapshot extends Model
{
    /** @use HasFactory<\Database\Factories\WeatherSnapshotFactory> */
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'recorded_at',
        'provider',
        'temperature',
        'humidity',
        'wind_speed',
        'conditions',
        'icon',
        'payload',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'temperature' => 'float',
        'humidity' => 'integer',
        'wind_speed' => 'float',
        'payload' => 'array',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class);
    }
}
