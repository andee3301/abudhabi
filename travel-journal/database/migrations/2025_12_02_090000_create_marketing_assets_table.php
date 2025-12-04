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
        Schema::create('marketing_assets', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('type');
            $table->string('label')->nullable();
            $table->string('path');
            $table->string('cdn_url')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_assets');
    }
};
