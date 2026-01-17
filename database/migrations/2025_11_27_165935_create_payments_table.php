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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses', 'id')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users', 'id')->onDelete('cascade');
            $table->string('midtrans_order_id');
            $table->decimal('gross_amount', 10, 2);
            $table->string('payment_type');
            $table->string('transaction_status');
            $table->string('fraud_status')->nullable();
            $table->integer('course_class_id')->nullable();
            $table->json('payment_payload')->nullable();
            $table->dateTime('settlement_at')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
