<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->text('depart_photo_path')->nullable()->change();
            $table->text('arrive_photo_path')->nullable()->change();
        });
        Schema::table('delivery_stops', function (Blueprint $table) {
            $table->text('arrive_photo_path')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->string('depart_photo_path')->nullable()->change();
            $table->string('arrive_photo_path')->nullable()->change();
        });
        Schema::table('delivery_stops', function (Blueprint $table) {
            $table->string('arrive_photo_path')->nullable()->change();
        });
    }
};
