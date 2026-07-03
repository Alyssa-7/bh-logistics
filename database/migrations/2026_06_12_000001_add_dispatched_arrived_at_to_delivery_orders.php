<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->timestamp('dispatched_at')->nullable()->after('delivered_at');
            $table->timestamp('arrived_at')->nullable()->after('received_at');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropColumn(['dispatched_at', 'arrived_at']);
        });
    }
};
