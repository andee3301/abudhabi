<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripEvent extends Model
{
    /** @use HasFactory<\Database\Factories\TripEventFactory> */
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'type',
        'title',
        'description',
        'start_time',
        'end_time',
        'location_data',
        'travel_method',
        'media_path',
        'position',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'location_data' => 'array',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
