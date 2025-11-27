<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('weather_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->timestamp('recorded_at');
            $table->string('provider')->default('openweathermap');
            $table->decimal('temperature', 5, 2)->nullable(); // Celsius
            $table->unsignedTinyInteger('humidity')->nullable();
            $table->decimal('wind_speed', 5, 2)->nullable();
            $table->string('conditions')->nullable();
            $table->string('icon')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->unique(['trip_id', 'recorded_at', 'provider']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_snapshots');
    }
};
