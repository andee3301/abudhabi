<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHomeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'home_city_id',
        'home_city_name',
        'home_country_code',
        'home_timezone',
        'preferred_currency',
        'locale',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function homeCity()
    {
        return $this->belongsTo(City::class, 'home_city_id');
    }
}
