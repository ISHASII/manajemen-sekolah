<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('training_class_id')->nullable();
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'sick', 'excused'])->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->index(['student_id', 'date']);
            $table->unique(['student_id', 'date', 'class_id', 'training_class_id'], 'attendance_unique_per_day');
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
