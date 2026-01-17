<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('quiz_assignments', function (Blueprint $table) {
        $table->dropForeign(['course_class_id']);
        $table->dropColumn('course_class_id');
    });
}

public function down()
{
    Schema::table('quiz_assignments', function (Blueprint $table) {
        $table->foreignId('course_class_id')->constrained('course_classes');
    });
}
};
