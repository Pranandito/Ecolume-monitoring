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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->foreign('owner_id')->references('id')->on('users')->nullOnDelete()->cascadeOnUpdate();
            $table->index('owner_id');

            $table->string('device_name', 25)->default('Portable-PTS');
            $table->string('serial_number', 15)->unique();
            $table->string('firmware_version', 10);
            $table->string('API_keys', 25);
            $table->boolean('online_status')->default(0);
            $table->timestamp('claim_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
