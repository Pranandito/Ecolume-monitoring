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
        Schema::table('devices_config', function (Blueprint $table) {
            $table->time('timer_start')->nullable()->after('location');
            $table->time('timer_end')->nullable()->after('timer_start');
            $table->unsignedInteger('volume_limit')->nullable()->after('timer_end');
            $table->enum('prev_mode', ['On', 'Off', 'Timer Waktu', 'Timer Volume'])->default('Off')->after('volume_limit');
            $table->boolean('job_confirmed')->default(false)->after('prev_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
