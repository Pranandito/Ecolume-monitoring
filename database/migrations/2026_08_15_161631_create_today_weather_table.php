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
        Schema::create('today_weather', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id')->nullable();
            $table->foreign('device_id')->references('id')->on('devices')->nullOnDelete()->cascadeOnUpdate();

            $table->date('date');

            $table->decimal('precipitation_probability_mean', 5, 2)->nullable();
            $table->decimal('relative_humidity_mean', 5, 2)->nullable();
            $table->decimal('wind_speed_mean', 6, 2)->nullable();
            $table->decimal('shortwave_radiation_sum', 8, 2)->nullable();
            $table->decimal('cloud_cover_mean', 5, 2)->nullable();

            $table->time('sunrise')->nullable();
            $table->time('sunset')->nullable();

            $table->decimal('temperature_mean', 5, 2)->nullable();
            $table->decimal('apparent_temperature_mean', 5, 2)->nullable();

            $table->unsignedSmallInteger('weather_code')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('today_weather');
    }
};
