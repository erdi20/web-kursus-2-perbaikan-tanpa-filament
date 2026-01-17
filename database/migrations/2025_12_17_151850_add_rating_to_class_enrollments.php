<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_xxxxxx_add_rating_to_class_enrollments.php
    public function up()
    {
        Schema::table('class_enrollments', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating')->nullable()->after('review');
        });
    }

    public function down()
    {
        Schema::table('class_enrollments', function (Blueprint $table) {
            $table->dropColumn('rating');
        });
    }
};
