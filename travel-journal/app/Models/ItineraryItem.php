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
        'itinerary_id',
        'city_id',
        'type',
        'title',
        'start_datetime',
        'end_datetime',
        'day_number',
        'sort_order',
        'location_name',
        'city',
        'state_region',
        'country_code',
        'timezone',
        'region_id',
        'address',
        'price',
        'currency',
        'status',
        'metadata',
        'links',
        'tags',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'metadata' => 'array',
        'links' => 'array',
        'tags' => 'array',
        'price' => 'decimal:2',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
