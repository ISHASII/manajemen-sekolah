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
        Schema::table('alumni', function (Blueprint $table) {
            $table->year('graduation_year')->nullable()->after('graduation_date');
            $table->decimal('final_grade', 5, 2)->nullable()->after('graduation_class');
            $table->text('notes')->nullable()->after('training_history');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumni', function (Blueprint $table) {
            $table->dropColumn(['graduation_year', 'final_grade', 'notes']);
        });
    }
};
