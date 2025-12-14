<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'country_code',
        'state_region',
        'timezone',
        'currency_code',
        'primary_language',
        'latitude',
        'longitude',
        'hero_image_url',
        'accent_color',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    protected static function booted(): void
    {
        static::creating(function (City $city) {
            if (! $city->slug) {
                $city->slug = Str::slug($city->name.'-'.$city->country_code);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function intel()
    {
        return $this->hasOne(CityIntel::class);
    }

    public function itineraries()
    {
        return $this->hasMany(Itinerary::class);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function itineraryItems()
    {
        return $this->hasMany(ItineraryItem::class);
    }

    public function getDisplayNameAttribute(): string
    {
        $parts = array_filter([$this->name, $this->state_region, strtoupper($this->country_code)]);

        return implode(', ', $parts);
    }
}
