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
        Schema::create('bids', function (Blueprint $table) {
            $table->uuid('id')->primary(); // ⬅️ UUID
            
            // ⭐️ ربط العرض بالمشروع اللي اتقدم عليه
            $table->foreignUuid('project_id')->constrained()->cascadeOnDelete();
            
            // ⭐️ ربط العرض بالمستقل اللي قدمه
            $table->foreignUuid('freelancer_id')->constrained('users')->cascadeOnDelete();
            
            $table->decimal('amount', 10, 2);
            $table->text('cover_letter');
            
            // حالة العرض: (مقدم، تم قبوله، تم رفضه)
            $table->enum('status', ['submitted', 'accepted', 'rejected'])->default('submitted');
    
            // لا يمكن للمستقل تقديم أكثر من عرض على نفس المشروع
            $table->unique(['project_id', 'freelancer_id']);
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
