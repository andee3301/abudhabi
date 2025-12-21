<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TripTimeline extends Model
{
    /** @use HasFactory<\Database\Factories\TripTimelineFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'trip_id',
        'user_id',
        'title',
        'description',
        'occurred_at',
        'type',
        'location_name',
        'tags',
        'metadata',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'tags' => 'array',
        'metadata' => 'array',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
