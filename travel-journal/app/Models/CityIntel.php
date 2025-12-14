<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityIntel extends Model
{
    use HasFactory;

    protected $table = 'city_intel';

    protected $fillable = [
        'city_id',
        'tagline',
        'summary',
        'local_time_label',
        'currency_code',
        'currency_rate',
        'electrical_plugs',
        'voltage',
        'emergency_numbers',
        'neighborhoods',
        'checklist',
        'cultural_notes',
        'weather',
        'visa',
        'best_months',
        'transport',
        'budget',
        'seasonality',
        'meta',
    ];

    protected $casts = [
        'emergency_numbers' => 'array',
        'neighborhoods' => 'array',
        'checklist' => 'array',
        'cultural_notes' => 'array',
        'weather' => 'array',
        'best_months' => 'array',
        'transport' => 'array',
        'budget' => 'array',
        'seasonality' => 'array',
        'meta' => 'array',
        'currency_rate' => 'float',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
