<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();

            // เพิ่มคอลัมน์ parent_order_id เพื่อผูกความสัมพันธ์ข้ามใบสั่งซื้อกรณีเป็น Backorder ของขาด
            $table->foreignId('parent_order_id')
                ->nullable()
                ->constrained('delivery_orders')
                ->nullOnDelete();

            $table->string('do_number')->unique();
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users');

            // อัปเดตเพิ่มสถานะ 'received_partial' (รับไม่ครบ) เข้าไปในระบบ Flow ออเดอร์
            $table->enum('status', ['pending', 'in_transit', 'delivered', 'received_partial', 'rejected'])->default('pending');

            $table->string('qr_code')->unique();
            $table->date('scheduled_date');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_orders');
    }
};
