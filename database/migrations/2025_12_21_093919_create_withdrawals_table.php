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
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('account_name');
            $table->string('account_number');
            $table->string('bank_name');
            $table->enum('status', ['pending', 'processed', 'completed'])->default('pending');
            $table->timestamp('processed_at')->nullable();  // saat admin transfer
            $table->timestamp('completed_at')->nullable();  // saat mentor konfirmasi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
