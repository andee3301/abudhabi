<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->json('city_stops')->nullable()->after('city_id');
            $table->json('wishlist_locations')->nullable()->after('city_stops');
            $table->string('location_overview')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn(['city_stops', 'wishlist_locations', 'location_overview']);
        });
    }
};
