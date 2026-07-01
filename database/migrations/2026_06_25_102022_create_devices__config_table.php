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
        Schema::create('devices_config', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id')->nullable();
            $table->foreign('device_id')->references('id')->on('devices')->nullOnDelete()->cascadeOnUpdate();
            $table->index('device_id');
            $table->enum('mode', ['On', 'Off', 'Timer Waktu', 'Timer Volume'])->default('Off');
            $table->decimal('lat', 10, 7)->default('-7.5618624');
            $table->decimal('long', 10, 7)->default('110.8538552');
            $table->string('location')->default('Jebres, Surakarta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices_config');
    }
};
