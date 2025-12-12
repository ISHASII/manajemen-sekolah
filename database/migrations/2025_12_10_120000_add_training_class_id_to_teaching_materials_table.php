<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teaching_materials', function (Blueprint $table) {
            $table->foreignId('training_class_id')->nullable()->constrained('training_classes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('teaching_materials', function (Blueprint $table) {
            $table->dropConstrainedForeignId('training_class_id');
        });
    }
};
