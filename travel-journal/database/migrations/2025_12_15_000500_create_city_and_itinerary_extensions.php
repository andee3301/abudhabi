<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('country_code', 2);
            $table->string('state_region')->nullable();
            $table->string('timezone', 64)->nullable();
            $table->string('currency_code', 3)->nullable();
            $table->string('primary_language', 64)->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->string('hero_image_url')->nullable();
            $table->string('accent_color', 16)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['country_code', 'name']);
        });

        Schema::create('city_intel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->string('tagline')->nullable();
            $table->text('summary')->nullable();
            $table->string('local_time_label')->nullable();
            $table->string('currency_code', 3)->nullable();
            $table->decimal('currency_rate', 10, 4)->nullable();
            $table->string('electrical_plugs')->nullable();
            $table->string('voltage')->nullable();
            $table->json('emergency_numbers')->nullable();
            $table->json('neighborhoods')->nullable();
            $table->json('checklist')->nullable();
            $table->json('cultural_notes')->nullable();
            $table->json('weather')->nullable();
            $table->string('visa')->nullable();
            $table->json('best_months')->nullable();
            $table->json('transport')->nullable();
            $table->json('budget')->nullable();
            $table->json('seasonality')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique('city_id');
        });

        Schema::create('itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('title');
            $table->unsignedInteger('day_count')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->string('theme')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['trip_id', 'start_date']);
        });

        Schema::create('user_home_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('home_city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('home_city_name')->nullable();
            $table->string('home_country_code', 2)->nullable();
            $table->string('home_timezone', 64)->nullable();
            $table->string('preferred_currency', 3)->nullable();
            $table->string('locale', 12)->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });

        Schema::create('electrical_standards', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 2)->unique();
            $table->string('plug_types')->nullable();
            $table->string('voltage')->nullable();
            $table->string('frequency')->nullable();
            $table->json('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 2);
            $table->string('city_name')->nullable();
            $table->string('service');
            $table->string('number');
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['country_code', 'city_name']);
        });

        Schema::table('trips', function (Blueprint $table) {
            $table->foreignId('city_id')->nullable()->after('primary_location_name')->constrained('cities')->nullOnDelete();
            $table->json('tags')->nullable()->after('notes');
        });

        Schema::table('itinerary_items', function (Blueprint $table) {
            $table->foreignId('itinerary_id')->nullable()->after('trip_id')->constrained('itineraries')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->after('itinerary_id')->constrained('cities')->nullOnDelete();
            $table->unsignedSmallInteger('day_number')->nullable()->after('end_datetime');
            $table->unsignedSmallInteger('sort_order')->default(0)->after('day_number');
            $table->json('links')->nullable()->after('metadata');
            $table->json('tags')->nullable()->after('links');
        });
    }

    public function down(): void
    {
        Schema::table('itinerary_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('city_id');
            $table->dropConstrainedForeignId('itinerary_id');
            $table->dropColumn(['day_number', 'sort_order', 'links', 'tags']);
        });

        Schema::table('trips', function (Blueprint $table) {
            $table->dropConstrainedForeignId('city_id');
            $table->dropColumn(['tags']);
        });

        Schema::dropIfExists('emergency_contacts');
        Schema::dropIfExists('electrical_standards');
        Schema::dropIfExists('user_home_settings');
        Schema::dropIfExists('itineraries');
        Schema::dropIfExists('city_intel');
        Schema::dropIfExists('cities');
    }
};
