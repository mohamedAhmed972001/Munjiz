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
        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('id')->primary(); // ⬅️ UUID
    
            // من قام بالتقييم (العميل أو المستقل)
            $table->foreignUuid('reviewer_id')->constrained('users')->cascadeOnDelete();
    
            // من تم تقييمه (المستقل أو العميل)
            $table->foreignUuid('reviewed_id')->constrained('users')->cascadeOnDelete();
    
            // المشروع الذي تم بناءً عليه التقييم (لمنع تقييم عشوائي)
            $table->foreignUuid('project_id')->constrained()->cascadeOnDelete();
            
            $table->unsignedTinyInteger('rating'); // ⬅️ رقم التقييم (مثلاً من 1 إلى 5)
            $table->text('comment');
            
            // لمنع تقييم نفس الشخص لنفس المشروع أكثر من مرة
            $table->unique(['reviewer_id', 'project_id']);
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
