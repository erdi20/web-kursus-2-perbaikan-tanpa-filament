<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tambahkan kolom penilaian ke `courses`
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedTinyInteger('essay_weight')->default(40)->after('discount_end_date');
            $table->unsignedTinyInteger('quiz_weight')->default(40)->after('essay_weight');
            $table->unsignedTinyInteger('attendance_weight')->default(20)->after('quiz_weight');
            $table->unsignedTinyInteger('min_attendance_percentage')->default(80)->after('attendance_weight');
            $table->unsignedTinyInteger('min_final_score')->default(70)->after('min_attendance_percentage');
        });

        // 2. Hapus kolom enrollment dari `courses`
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['enrollment_start', 'enrollment_end']);
        });

        // 3. Tambahkan kolom enrollment ke `course_classes`
        Schema::table('course_classes', function (Blueprint $table) {
            $table->dateTime('enrollment_start')->nullable()->after('max_quota');
            $table->dateTime('enrollment_end')->nullable()->after('enrollment_start');
        });

        // 4. Hapus kolom penilaian dari `course_classes`
        Schema::table('course_classes', function (Blueprint $table) {
            $table->dropColumn([
                'essay_weight',
                'quiz_weight',
                'attendance_weight',
                'min_attendance_percentage',
                'min_final_score',
            ]);
        });
    }

    public function down()
    {
        // Rollback
        Schema::table('courses', function (Blueprint $table) {
            $table->dateTime('enrollment_start')->nullable();
            $table->dateTime('enrollment_end')->nullable();
            $table->dropColumn([
                'essay_weight',
                'quiz_weight',
                'attendance_weight',
                'min_attendance_percentage',
                'min_final_score',
            ]);
        });

        Schema::table('course_classes', function (Blueprint $table) {
            $table->dropColumn(['enrollment_start', 'enrollment_end']);
            $table->unsignedTinyInteger('essay_weight')->default(40);
            $table->unsignedTinyInteger('quiz_weight')->default(40);
            $table->unsignedTinyInteger('attendance_weight')->default(20);
            $table->unsignedTinyInteger('min_attendance_percentage')->default(80);
            $table->unsignedTinyInteger('min_final_score')->default(70);
        });
    }
};
