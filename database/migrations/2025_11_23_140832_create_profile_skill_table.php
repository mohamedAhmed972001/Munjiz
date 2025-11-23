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
        Schema::create('profile_skill', function (Blueprint $table) {
            $table->id(); // الـ Pivot ID عادي يكون رقم
            
            // ⚠️ أهم نقطة: الربط بـ foreignUuid
            $table->foreignUuid('profile_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('skill_id')->constrained()->cascadeOnDelete();
            
            // منع تكرار نفس المهارة لنفس البروفايل
            $table->unique(['profile_id', 'skill_id']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_skill');
    }
};
