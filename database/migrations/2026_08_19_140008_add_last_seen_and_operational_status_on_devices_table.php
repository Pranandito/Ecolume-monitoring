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
        Schema::table('devices', function (Blueprint $table) {
            $table->enum('operational_status', ['On', 'Off', 'Anomaly'])->default('Off')->after('online_status');
            $table->timestamp('last_seen')->nullable()->after('operational_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('operational_status');
            $table->dropColumn('last_seen');
        });
    }
};
