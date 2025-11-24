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
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary(); // ⬅️ UUID
            
            // ⭐️ ربط المشروع بالعميل الذي قام بإنشائه (العميل هو User)
            $table->foreignUuid('client_id')->constrained('users')->cascadeOnDelete();
            
            $table->string('title');
            $table->text('description');
            $table->decimal('budget', 10, 2);
            
            // حالة المشروع: (مفتوح، قيد التنفيذ، مكتمل، ملغي)
            $table->enum('status', ['open', 'in_progress', 'completed', 'cancelled'])->default('open');
            
            // لو تم اختيار فائز، هنحط الـ ID بتاعه هنا
            $table->foreignUuid('freelancer_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
