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
        Schema::create('student_grade_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->string('class_name'); // Nama kelas saat itu (misal: "1 SD", "2 SMP")
            $table->year('academic_year'); // Tahun ajaran
            $table->enum('semester', ['1', '2']); // Semester 1 atau 2
            $table->decimal('average_grade', 5, 2)->nullable(); // Rata-rata nilai
            $table->text('subjects_grades')->nullable(); // JSON untuk detail nilai per mata pelajaran
            $table->enum('status', ['passed', 'failed', 'in_progress'])->default('in_progress'); // Status kelulusan
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->date('completed_at')->nullable(); // Tanggal selesai
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_grade_history');
    }
};
