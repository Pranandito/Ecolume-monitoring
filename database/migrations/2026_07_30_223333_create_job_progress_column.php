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
            $table->dropColumn('prev_mode');
            $table->float('volume_progress')->after('volume_limit')->default(0);
            $table->unsignedInteger('job_id')->nullable()->after('job_confirmed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices_config', function (Blueprint $table) {
            $table->string('prev_mode')->nullable(); // sesuaikan tipe data aslinya
            $table->dropColumn('volume_progress');
            $table->dropColumn('job_id');
        });
    }
};
