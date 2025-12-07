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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('teacher_id')->unique();
            $table->string('nip')->nullable();
            $table->json('subjects')->nullable(); // Mata pelajaran yang diajar
            $table->json('qualifications')->nullable(); // Kualifikasi pendidikan
            $table->json('certifications')->nullable(); // Sertifikat
            $table->date('hire_date');
            $table->enum('status', ['active', 'inactive', 'retired'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
