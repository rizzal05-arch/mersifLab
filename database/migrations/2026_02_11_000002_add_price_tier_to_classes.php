<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->string('price_tier')->nullable()->after('price');
        });
    }

    public function down()
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn('price_tier');
        });
    }
};
