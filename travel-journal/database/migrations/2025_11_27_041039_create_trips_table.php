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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('primary_location_name');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('cover_image_url')->nullable();
            $table->string('status')->default('planned'); // planned, ongoing, completed
            $table->string('companion_name')->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'start_date']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
