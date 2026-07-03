<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('vehicles');
    }

    public function down(): void
    {
        Schema::create('vehicles', function ($table) {
            $table->id();
            $table->string('license_plate')->unique();
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }
};
