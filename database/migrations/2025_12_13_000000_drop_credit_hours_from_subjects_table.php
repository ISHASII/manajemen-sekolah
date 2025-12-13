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
        if (Schema::hasTable('subjects') && Schema::hasColumn('subjects', 'credit_hours')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->dropColumn('credit_hours');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('subjects') && !Schema::hasColumn('subjects', 'credit_hours')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->integer('credit_hours')->default(1);
            });
        }
    }
};
