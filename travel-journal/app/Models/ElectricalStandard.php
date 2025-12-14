<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectricalStandard extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_code',
        'plug_types',
        'voltage',
        'frequency',
        'notes',
    ];

    protected $casts = [
        'notes' => 'array',
    ];
}
