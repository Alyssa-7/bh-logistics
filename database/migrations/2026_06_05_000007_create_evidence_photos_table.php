<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidence_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receive_record_id')->constrained('receive_records')->cascadeOnDelete();
            $table->string('photo_path');
            $table->timestamp('uploaded_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidence_photos');
    }
};
