<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->string('depart_photo_path')->nullable()->after('arrived_at');
            $table->timestamp('checked_at')->nullable()->after('depart_photo_path');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropColumn(['depart_photo_path', 'checked_at']);
        });
    }
};
