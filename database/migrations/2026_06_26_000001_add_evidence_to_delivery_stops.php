<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_stops', function (Blueprint $table) {
            $table->string('arrive_photo_path')->nullable()->after('delivered_at');
            $table->text('signature')->nullable()->after('arrive_photo_path');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_stops', function (Blueprint $table) {
            $table->dropColumn(['arrive_photo_path', 'signature']);
        });
    }
};
