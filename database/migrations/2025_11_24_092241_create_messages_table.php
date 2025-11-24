<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary(); // ⬅️ UUID
    
            // المشروع الذي يخصه المحادثة (الرسالة يجب أن تكون مرتبطة بمشروع معين)
            $table->foreignUuid('project_id')->constrained()->cascadeOnDelete();
    
            // مرسل الرسالة
            $table->foreignUuid('sender_id')->constrained('users')->cascadeOnDelete();
    
            // محتوى الرسالة
            $table->text('content');
            
            // يمكن إضافة حالة للقراءة لاحقاً (is_read)
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
