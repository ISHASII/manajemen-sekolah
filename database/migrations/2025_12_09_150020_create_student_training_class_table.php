<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('student_training_class', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('training_class_id');
            $table->timestamp('enrolled_at')->nullable();
            $table->string('status')->default('enrolled'); // enrolled, completed, canceled
            $table->timestamps();

            $table->unique(['student_id', 'training_class_id']);
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('training_class_id')->references('id')->on('training_classes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_training_class');
    }
};
