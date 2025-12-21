<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TripNote extends Model
{
    /** @use HasFactory<\Database\Factories\TripNoteFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'trip_id',
        'user_id',
        'title',
        'body',
        'note_date',
        'is_pinned',
        'tags',
        'metadata',
    ];

    protected $casts = [
        'note_date' => 'date',
        'is_pinned' => 'boolean',
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
