<?php

namespace App\Models;

use App\Support\MarketingAssetRepository;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Trip extends Model
{
    /** @use HasFactory<\Database\Factories\TripFactory> */
    use HasFactory;

    protected const COVER_FALLBACKS = [
        'planned' => 'trip_cover_default',
        'completed' => 'trip_cover_default',
        'ongoing' => 'trip_cover_sunset',
        'active' => 'trip_cover_sunset',
    ];

    protected $fillable = [
        'user_id',
        'title',
        'primary_location_name',
        'start_date',
        'end_date',
        'status',
        'companion_name',
        'notes',
        'cover_image_url',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function itineraryItems()
    {
        return $this->hasMany(ItineraryItem::class);
    }

    public function housing()
    {
        return $this->itineraryItems()->where('type', 'housing');
    }

    public function transport()
    {
        return $this->itineraryItems()->where('type', 'transport');
    }

    public function activities()
    {
        return $this->itineraryItems()->where('type', 'activity');
    }

    public function countryVisits()
    {
        return $this->hasMany(CountryVisit::class);
    }

    public function getCoverUrlAttribute(): string
    {
        $source = $this->cover_image_url;

        if ($source) {
            if (Str::startsWith($source, ['http://', 'https://'])) {
                return $source;
            }

            return asset($source);
        }

        $fallbackKey = self::COVER_FALLBACKS[$this->status] ?? 'trip_cover_default';

        return app(MarketingAssetRepository::class)->url($fallbackKey);
    }
}
