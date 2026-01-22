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
        if (Schema::hasTable('modules') && !Schema::hasColumn('modules', 'estimated_duration')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->integer('estimated_duration')->default(0)->after('order')->comment('Estimated duration in minutes');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('modules') && Schema::hasColumn('modules', 'estimated_duration')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->dropColumn('estimated_duration');
            });
        }
    }
};
