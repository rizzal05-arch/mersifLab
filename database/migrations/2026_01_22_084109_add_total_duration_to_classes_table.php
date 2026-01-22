<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('classes') && !Schema::hasColumn('classes', 'total_duration')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->integer('total_duration')->default(0)->after('price')->comment('Total duration from all chapters in minutes');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('classes') && Schema::hasColumn('classes', 'total_duration')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->dropColumn('total_duration');
            });
        }
    }
};
