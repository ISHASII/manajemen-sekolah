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
        Schema::create('alumni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->date('graduation_date');
            $table->string('graduation_class');
            $table->json('skills')->nullable(); // Keahlian yang dimiliki
            $table->json('portfolio')->nullable(); // Portfolio
            $table->json('work_interests')->nullable(); // Minat kerja
            $table->string('current_job')->nullable();
            $table->string('current_company')->nullable();
            $table->text('cv_online')->nullable();
            $table->string('linkedin_profile')->nullable();
            $table->json('achievements')->nullable();
            $table->json('training_history')->nullable(); // Riwayat pelatihan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni');
    }
};
