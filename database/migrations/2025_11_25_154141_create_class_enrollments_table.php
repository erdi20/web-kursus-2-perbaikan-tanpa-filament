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
        Schema::create('class_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('course_classes', 'id')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->dateTime('enrolled_at');
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->dateTime('completed_at')->nullable();
            $table->enum('status', ['active',
                'completed', 'dropped']);
            $table->integer('grade')->nullable();
            $table->string('certificate')->nullable();
            $table->dateTime('issued_at')->nullable();
            $table->boolean('is_verified')->default(false)->nullable();
            $table->text('review')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_enrollments');
    }
};
