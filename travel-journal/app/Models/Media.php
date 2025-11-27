<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    /** @use HasFactory<\Database\Factories\MediaFactory> */
    use HasFactory;

    protected $fillable = [
        'journal_entry_id',
        'disk',
        'path',
        'mime_type',
        'size',
        'width',
        'height',
        'caption',
    ];

    protected $casts = [
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }
}
