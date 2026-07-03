<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add received_incomplete to status ENUM
        DB::statement("ALTER TABLE delivery_orders
            MODIFY COLUMN status ENUM(
                'pending',
                'in_transit',
                'delivered',
                'rejected',
                'cancelled',
                'received_partial',
                'received_incomplete'
            ) NOT NULL DEFAULT 'pending'");

        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_do_id')->nullable()->after('created_by');
            $table->boolean('is_backorder')->default(false)->after('parent_do_id');

            $table->foreign('parent_do_id')
                ->references('id')
                ->on('delivery_orders')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropForeign(['parent_do_id']);
            $table->dropColumn(['parent_do_id', 'is_backorder']);
        });

        DB::statement("ALTER TABLE delivery_orders
            MODIFY COLUMN status ENUM(
                'pending',
                'in_transit',
                'delivered',
                'rejected',
                'cancelled',
                'received_partial'
            ) NOT NULL DEFAULT 'pending'");
    }
};
