<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('job_interest')->nullable()->after('interests_talents');
            $table->string('cv_link')->nullable()->after('job_interest');
            $table->json('portfolio_links')->nullable()->after('cv_link');
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['job_interest', 'cv_link', 'portfolio_links']);
        });
    }
};
