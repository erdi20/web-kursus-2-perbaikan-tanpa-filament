<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('course_classes', function (Blueprint $table) {
            // Bobot nilai (dalam persen, total harus = 100)
            $table->unsignedTinyInteger('essay_weight')->default(40)->comment('Bobot essay (%)');
            $table->unsignedTinyInteger('quiz_weight')->default(40)->comment('Bobot quiz (%)');
            $table->unsignedTinyInteger('attendance_weight')->default(20)->comment('Bobot absensi (%)');

            // Kriteria kelulusan
            $table->unsignedTinyInteger('min_attendance_percentage')->default(80)->comment('Minimal kehadiran (%)');
            $table->unsignedTinyInteger('min_final_score')->default(70)->comment('Nilai minimal untuk lulus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
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
};
