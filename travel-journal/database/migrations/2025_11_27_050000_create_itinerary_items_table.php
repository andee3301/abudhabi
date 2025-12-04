<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itinerary_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // transport, housing, activity, note
            $table->string('title');
            $table->dateTime('start_datetime')->nullable();
            $table->dateTime('end_datetime')->nullable();
            $table->string('location_name')->nullable();
            $table->string('address')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->string('status')->nullable(); // booked, tentative, completed
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['trip_id', 'type']);
            $table->index(['start_datetime']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itinerary_items');
    }
};
