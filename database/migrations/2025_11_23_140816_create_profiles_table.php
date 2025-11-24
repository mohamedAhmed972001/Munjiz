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
      Schema::create('profiles', function (Blueprint $table) {
        $table->uuid('id')->primary(); // ⬅️ UUID
        $table->foreignUuid('user_id')->constrained()->cascadeOnDelete(); // ⬅️ ربطنا بـ UUID اليوزر
        $table->string('job_title')->nullable();
        $table->text('bio')->nullable();
        $table->string('github_link')->nullable();
        $table->string('linkedin_link')->nullable();
        $table->decimal('wallet_balance', 10, 2)->default(0); // ⬅️ جهزنا المحفظة للمستقبل
        $table->timestamps();
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
