<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->enum('backorder_status', ['pending_approval', 'approved', 'rejected'])
                ->nullable()
                ->after('is_backorder');
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->after('backorder_status');
            $table->text('rejection_reason')
                ->nullable()
                ->after('approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['backorder_status', 'approved_by', 'rejection_reason']);
        });
    }
};
