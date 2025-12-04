<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('country_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->string('country_code', 3);
            $table->string('city_name')->nullable();
            $table->date('visited_at')->nullable();
            $table->timestamps();

            $table->index(['trip_id', 'country_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_visits');
    }
};
