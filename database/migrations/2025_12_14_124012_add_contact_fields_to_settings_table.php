<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('email')->nullable()->after('copyright_text');
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->text('gmaps_embed_url')->nullable()->after('address');
        });
    }

    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['email', 'phone', 'address', 'gmaps_embed_url']);
        });
    }
};
