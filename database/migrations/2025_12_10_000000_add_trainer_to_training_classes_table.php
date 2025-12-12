<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('training_classes', function (Blueprint $table) {
            $table->unsignedBigInteger('trainer_id')->nullable()->after('created_by');
            $table->foreign('trainer_id')->references('id')->on('teachers')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('training_classes', function (Blueprint $table) {
            $table->dropForeign(['trainer_id']);
            $table->dropColumn('trainer_id');
        });
    }
};
