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
        Schema::create('student_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique();
            $table->string('student_name');
            $table->string('email');
            $table->string('phone');
            $table->string('nisn')->nullable();
            $table->string('place_of_birth');
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female']);
            $table->enum('religion', ['islam', 'kristen', 'katolik', 'hindu', 'budha', 'khonghucu']);
            $table->text('address');
            $table->string('parent_name');
            $table->string('parent_phone');
            $table->text('parent_address');
            $table->string('parent_job')->nullable();
            $table->json('health_info')->nullable();
            $table->json('disability_info')->nullable();
            $table->json('education_history')->nullable();
            $table->string('desired_class');
            $table->json('additional_info')->nullable();
            $table->json('documents')->nullable(); // File dokumen yang diupload
            $table->enum('status', ['pending', 'approved', 'rejected', 'waiting_payment'])->default('pending');
            $table->text('notes')->nullable();
            $table->date('application_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_applications');
    }
};
