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
        Schema::create('course_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->foreignId('course_id')->constrained('courses', 'id')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users', 'id')->onDelete('cascade');
            $table->enum('status', ['draft', 'open', 'closed', 'archived']);
            $table->integer('max_quota');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_classes');
    }
};
