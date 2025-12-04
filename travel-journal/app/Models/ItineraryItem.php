<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItineraryItem extends Model
{
    /** @use HasFactory<\Database\Factories\ItineraryItemFactory> */
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'type',
        'title',
        'start_datetime',
        'end_datetime',
        'location_name',
        'address',
        'price',
        'currency',
        'status',
        'metadata',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'metadata' => 'array',
        'price' => 'decimal:2',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
