<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shortage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('do_id')
                ->constrained('delivery_orders')
                ->cascadeOnDelete();
            $table->foreignId('item_id')
                ->constrained('delivery_items')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnDelete();
            $table->decimal('shortage_qty', 10, 2);
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shortage_logs');
    }
};
