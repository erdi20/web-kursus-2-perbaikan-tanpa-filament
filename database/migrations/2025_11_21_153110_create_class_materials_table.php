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
        Schema::create('class_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_class_id')->constrained('course_classes')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');

            $table->unsignedInteger('order')->default(1);  // Urutan materi dalam kelas
            $table->dateTime('schedule_date')->nullable();  // Tanggal/waktu materi ini harus dibuka/dipresentasikan
            $table->enum('visibility', ['hidden', 'visible'])->default('visible');

            $table->unique(['course_class_id', 'material_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_materials');
    }
};
