<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // delivery_orders — composite indexes for common query patterns
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->index(['vehicle_id', 'status'],    'do_vehicle_status');
            $table->index(['branch_id',  'status'],    'do_branch_status');
            $table->index(['status',     'created_at'],'do_status_created');
            $table->index(['scheduled_date', 'status'],'do_sched_status');
        });

        // gps_logs — latest-per-vehicle is the hottest query (every GPS ping)
        Schema::table('gps_logs', function (Blueprint $table) {
            $table->index(['vehicle_id', 'recorded_at'], 'gps_vehicle_recorded');
        });

        // notifications — per-user listing ordered by created_at
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'notif_user_created');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropIndex('do_vehicle_status');
            $table->dropIndex('do_branch_status');
            $table->dropIndex('do_status_created');
            $table->dropIndex('do_sched_status');
        });

        Schema::table('gps_logs', function (Blueprint $table) {
            $table->dropIndex('gps_vehicle_recorded');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notif_user_created');
        });
    }
};
