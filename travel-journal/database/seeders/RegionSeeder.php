<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            ['country_code' => 'PT', 'code' => 'LIS', 'name' => 'Lisboa', 'default_timezone' => 'Europe/Lisbon'],
            ['country_code' => 'JP', 'code' => 'KAN', 'name' => 'Kansai', 'default_timezone' => 'Asia/Tokyo'],
            ['country_code' => 'MX', 'code' => 'OAX', 'name' => 'Oaxaca', 'default_timezone' => 'America/Mexico_City'],
            ['country_code' => 'IS', 'code' => 'REK', 'name' => 'Capital Region', 'default_timezone' => 'Atlantic/Reykjavik'],
            ['country_code' => 'MA', 'code' => 'MRK', 'name' => 'Marrakech-Safi', 'default_timezone' => 'Africa/Casablanca'],
            ['country_code' => 'ZA', 'code' => 'CPT', 'name' => 'Western Cape', 'default_timezone' => 'Africa/Johannesburg'],
            ['country_code' => 'CA', 'code' => 'BC', 'name' => 'British Columbia', 'default_timezone' => 'America/Vancouver'],
            ['country_code' => 'KR', 'code' => 'SEO', 'name' => 'Seoul Capital Area', 'default_timezone' => 'Asia/Seoul'],
            ['country_code' => 'IT', 'code' => 'TOS', 'name' => 'Tuscany', 'default_timezone' => 'Europe/Rome'],
            ['country_code' => 'NZ', 'code' => 'OTA', 'name' => 'Otago', 'default_timezone' => 'Pacific/Auckland'],
            ['country_code' => 'AR', 'code' => 'BUE', 'name' => 'Buenos Aires Province', 'default_timezone' => 'America/Argentina/Buenos_Aires'],
            ['country_code' => 'IN', 'code' => 'RJ', 'name' => 'Rajasthan', 'default_timezone' => 'Asia/Kolkata'],
            ['country_code' => 'US', 'code' => 'CA', 'name' => 'California', 'default_timezone' => 'America/Los_Angeles'],
            ['country_code' => 'US', 'code' => 'NY', 'name' => 'New York', 'default_timezone' => 'America/New_York'],
        ];

        foreach ($regions as $region) {
            Region::updateOrCreate(
                ['country_code' => $region['country_code'], 'code' => $region['code']],
                $region
            );
        }
    }
}
