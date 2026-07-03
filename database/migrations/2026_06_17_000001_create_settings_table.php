<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Default HQ location (Bangkok center — admin should update this)
        DB::table('settings')->insert([
            ['key' => 'hq_lat',  'value' => '13.7563', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'hq_lng',  'value' => '100.5018','created_at' => now(), 'updated_at' => now()],
            ['key' => 'hq_name', 'value' => 'คลังสินค้ากลาง (HQ)', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
