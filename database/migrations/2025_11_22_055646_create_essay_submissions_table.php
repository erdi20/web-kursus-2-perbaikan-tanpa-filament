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
        Schema::create('essay_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('essay_assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->text('answer_text')->nullable();
            $table->string('file_path')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->boolean('is_graded')->default(false);
            $table->integer('score')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->unique(['essay_assignment_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('essay_submissions');
    }
};
