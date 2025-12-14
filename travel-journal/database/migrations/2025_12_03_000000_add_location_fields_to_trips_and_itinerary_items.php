<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 2);
            $table->string('code', 32)->nullable();
            $table->string('name');
            $table->string('default_timezone', 64)->nullable();
            $table->timestamps();

            $table->unique(['country_code', 'code']);
            $table->index(['country_code', 'name']);
        });

        Schema::table('trips', function (Blueprint $table) {
            $table->string('city')->nullable()->after('primary_location_name');
            $table->string('state_region')->nullable()->after('city');
            $table->string('country_code', 2)->nullable()->after('state_region');
            $table->string('timezone', 64)->nullable()->after('country_code');
            $table->foreignId('region_id')->nullable()->after('timezone')->constrained()->nullOnDelete();

            $table->index(['country_code', 'start_date']);
            $table->index(['timezone']);
        });

        Schema::table('itinerary_items', function (Blueprint $table) {
            $table->string('city')->nullable()->after('location_name');
            $table->string('state_region')->nullable()->after('city');
            $table->string('country_code', 2)->nullable()->after('state_region');
            $table->string('timezone', 64)->nullable()->after('country_code');
            $table->foreignId('region_id')->nullable()->after('timezone')->constrained()->nullOnDelete();
            $table->index(['trip_id', 'start_datetime']);
        });

        Schema::table('country_visits', function (Blueprint $table) {
            $table->string('state_region')->nullable()->after('city_name');
            $table->string('timezone', 64)->nullable()->after('state_region');
            $table->foreignId('region_id')->nullable()->after('timezone')->constrained()->nullOnDelete();

            $table->index(['country_code']);
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropIndex(['country_code', 'start_date']);
            $table->dropIndex(['timezone']);

            $table->dropConstrainedForeignId('region_id');
            $table->dropColumn(['city', 'state_region', 'country_code', 'timezone']);
        });

        Schema::table('itinerary_items', function (Blueprint $table) {
            $table->dropIndex(['trip_id', 'start_datetime']);
            $table->dropConstrainedForeignId('region_id');
            $table->dropColumn(['city', 'state_region', 'country_code', 'timezone']);
        });

        Schema::table('country_visits', function (Blueprint $table) {
            $table->dropIndex(['country_code']);
            $table->dropConstrainedForeignId('region_id');
            $table->dropColumn(['state_region', 'timezone']);
        });

        Schema::dropIfExists('regions');
    }
};
