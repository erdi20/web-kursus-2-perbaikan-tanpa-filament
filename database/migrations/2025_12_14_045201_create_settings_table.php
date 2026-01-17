<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name');
            $table->text('site_description');
            $table->string('logo')->nullable();  // path di storage
            $table->string('facebook_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('copyright_text');
            $table->timestamps();
        });

        // Insert 1 record default
        DB::table('settings')->insert([
            'site_name' => 'Qualitative Research Class',
            'site_description' => 'Membantu Anda menguasai metode penelitian kualitatif dengan panduan praktis dan dukungan komunitas.',
            'copyright_text' => '© ' . date('Y') . ' Qualitative Research Class. All rights reserved.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
