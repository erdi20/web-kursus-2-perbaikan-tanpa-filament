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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_material_id')->constrained('class_materials')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');  // karena siswa = User

            $table->string('photo_path')->nullable();  // path ke foto di storage
            $table->timestamp('attended_at');
            $table->unique(['class_material_id', 'student_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
