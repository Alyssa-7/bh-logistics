<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
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

    public function down(): void
    {
        DB::statement("ALTER TABLE delivery_orders
            MODIFY COLUMN status ENUM(
                'pending',
                'in_transit',
                'delivered',
                'rejected',
                'cancelled'
            ) NOT NULL DEFAULT 'pending'");
    }
};
