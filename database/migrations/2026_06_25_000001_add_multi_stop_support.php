<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make branch_id nullable for multi-stop orders (branch_id will be null on the parent order)
        DB::statement('ALTER TABLE delivery_orders MODIFY branch_id BIGINT UNSIGNED NULL');

        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->boolean('is_multi_stop')->default(false)->after('branch_id');
        });

        Schema::create('delivery_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches');
            $table->unsignedSmallInteger('sequence')->default(1);
            $table->enum('status', ['pending', 'delivered', 'rejected'])->default('pending');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });

        Schema::table('delivery_items', function (Blueprint $table) {
            $table->foreignId('stop_id')
                ->nullable()
                ->constrained('delivery_stops')
                ->nullOnDelete()
                ->after('delivery_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_items', function (Blueprint $table) {
            $table->dropForeign(['stop_id']);
            $table->dropColumn('stop_id');
        });
        Schema::dropIfExists('delivery_stops');
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropColumn('is_multi_stop');
        });
        DB::statement('ALTER TABLE delivery_orders MODIFY branch_id BIGINT UNSIGNED NOT NULL');
    }
};
