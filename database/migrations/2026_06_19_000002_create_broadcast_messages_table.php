<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcast_messages', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->string('target_roles', 255); // JSON array
            $table->enum('priority', ['normal', 'important', 'urgent'])->default('normal');
            $table->unsignedInteger('recipient_count')->default(0);
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_messages');
    }
};
