<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_code',
        'code',
        'name',
        'default_timezone',
    ];

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function itineraryItems()
    {
        return $this->hasMany(ItineraryItem::class);
    }

    public function countryVisits()
    {
        return $this->hasMany(CountryVisit::class);
    }
}
