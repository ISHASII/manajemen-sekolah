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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('student_id')->unique();
            $table->foreignId('class_id')->nullable()->constrained()->onDelete('set null');
            $table->string('nisn')->nullable()->unique();
            $table->string('place_of_birth');
            $table->date('birth_date');
            $table->enum('religion', ['islam', 'kristen', 'katolik', 'hindu', 'budha', 'khonghucu']);
            $table->text('address');
            $table->string('parent_name');
            $table->string('parent_phone');
            $table->text('parent_address');
            $table->string('parent_job')->nullable();
            $table->json('health_info')->nullable();
            $table->json('disability_info')->nullable();
            $table->json('education_history')->nullable();
            $table->json('interests_talents')->nullable();
            $table->enum('status', ['active', 'inactive', 'graduated', 'transferred'])->default('active');
            $table->boolean('is_orphan')->default(false);
            $table->date('enrollment_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
