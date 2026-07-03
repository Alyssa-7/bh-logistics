<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── delivery_orders ──────────────────────────────────────────
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete()->after('branch_id');
        });

        // migrate existing assignments: copy driver from the vehicle record
        DB::statement('
            UPDATE delivery_orders do2
            LEFT JOIN vehicles v ON do2.vehicle_id = v.id
            SET do2.driver_id = v.driver_id
            WHERE do2.vehicle_id IS NOT NULL
        ');

        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->dropColumn('vehicle_id');
        });

        // ── gps_logs ─────────────────────────────────────────────────
        Schema::table('gps_logs', function (Blueprint $table) {
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete()->after('id');
        });

        DB::statement('
            UPDATE gps_logs gl
            LEFT JOIN vehicles v ON gl.vehicle_id = v.id
            SET gl.driver_id = v.driver_id
        ');

        Schema::table('gps_logs', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->dropColumn('vehicle_id');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete()->after('branch_id');
        });
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropColumn('driver_id');
        });

        Schema::table('gps_logs', function (Blueprint $table) {
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete()->after('id');
        });
        Schema::table('gps_logs', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropColumn('driver_id');
        });
    }
};
