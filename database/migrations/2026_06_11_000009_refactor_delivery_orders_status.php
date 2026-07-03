<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop duplicate parent_order_id (was in original create; parent_do_id added later)
        if (Schema::hasColumn('delivery_orders', 'parent_order_id')) {
            Schema::table('delivery_orders', function (Blueprint $table) {
                $table->dropForeign(['parent_order_id']);
                $table->dropColumn('parent_order_id');
            });
        }

        // 2. Drop old backorder_status column (replaced by sub_status)
        if (Schema::hasColumn('delivery_orders', 'backorder_status')) {
            Schema::table('delivery_orders', function (Blueprint $table) {
                $table->dropColumn('backorder_status');
            });
        }

        // 3. Map legacy status values before modifying ENUM
        DB::statement("UPDATE delivery_orders SET status = 'problem'
            WHERE status IN ('rejected','received_partial','received_incomplete')");

        // 4. Rebuild status ENUM with clean set
        DB::statement("ALTER TABLE delivery_orders
            MODIFY COLUMN status ENUM(
                'pending',
                'in_transit',
                'delivered',
                'completed',
                'problem',
                'cancelled'
            ) NOT NULL DEFAULT 'pending'");

        // 5. Add new columns
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->enum('sub_status', [
                'incomplete',
                'rejected',
                'bo_pending',
                'bo_approved',
                'bo_rejected',
            ])->nullable()->after('status');

            $table->tinyInteger('bo_round')->unsigned()->default(0)->after('is_backorder');

            $table->timestamp('received_at')->nullable()->after('delivered_at');
        });

        // 6. Backfill sub_status from old backorder approval data
        DB::statement("UPDATE delivery_orders
            SET sub_status = 'bo_approved'
            WHERE is_backorder = 1 AND approved_by IS NOT NULL AND rejection_reason IS NULL");

        DB::statement("UPDATE delivery_orders
            SET sub_status = 'bo_rejected'
            WHERE is_backorder = 1 AND rejection_reason IS NOT NULL");

        DB::statement("UPDATE delivery_orders
            SET sub_status = 'bo_pending'
            WHERE is_backorder = 1 AND sub_status IS NULL");
    }

    public function down(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropColumn(['sub_status', 'bo_round', 'received_at']);
        });

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
            $table->enum('backorder_status', ['pending_approval','approved','rejected'])
                ->nullable()->after('is_backorder');
            $table->foreignId('parent_order_id')
                ->nullable()->constrained('delivery_orders')->nullOnDelete()->after('id');
        });
    }
};
