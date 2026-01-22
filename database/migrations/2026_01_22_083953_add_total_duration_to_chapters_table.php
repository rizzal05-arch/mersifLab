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
        if (Schema::hasTable('chapters') && !Schema::hasColumn('chapters', 'total_duration')) {
            Schema::table('chapters', function (Blueprint $table) {
                $table->integer('total_duration')->default(0)->after('order')->comment('Total duration from all modules in minutes');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('chapters') && Schema::hasColumn('chapters', 'total_duration')) {
            Schema::table('chapters', function (Blueprint $table) {
                $table->dropColumn('total_duration');
            });
        }
    }
};
