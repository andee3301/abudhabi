<?php

namespace App\Services;

use App\Models\City;
use Illuminate\Database\Eloquent\Collection;

class GeoLookupService
{
    public function search(string $query = '', int $limit = 8): Collection
    {
        return City::query()
            ->when($query, function ($builder) use ($query) {
                $builder->where(function ($q) use ($query) {
                    $q->where('name', 'like', '%'.$query.'%')
                        ->orWhere('state_region', 'like', '%'.$query.'%')
                        ->orWhere('country_code', 'like', '%'.$query.'%');
                });
            })
            ->orderByRaw('CASE WHEN name LIKE ? THEN 0 ELSE 1 END', [$query.'%'])
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    public function findBySlugOrName(string $value): ?City
    {
        return City::where('slug', $value)
            ->orWhere('name', 'like', '%'.$value.'%')
            ->first();
    }
}
