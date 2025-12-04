<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryVisit extends Model
{
    /** @use HasFactory<\Database\Factories\CountryVisitFactory> */
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'country_code',
        'city_name',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'date',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
