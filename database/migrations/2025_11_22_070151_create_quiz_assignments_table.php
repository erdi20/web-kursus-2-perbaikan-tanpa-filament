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
        Schema::create('quiz_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_class_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();  // Deskripsi umum quiz
            $table->dateTime('due_date');
            $table->integer('duration_minutes')->nullable();  // Bisa digunakan untuk timer
            $table->boolean('is_published')->default(false);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_assignments');
    }
};
