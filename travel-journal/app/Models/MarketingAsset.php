<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'type',
        'label',
        'path',
        'cdn_url',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
