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
        Schema::table('modules', function (Blueprint $table) {
            $table->integer('estimated_duration')->default(0)->after('order')->comment('Estimated duration in minutes');
        });

        Schema::table('chapters', function (Blueprint $table) {
            $table->integer('total_duration')->default(0)->after('order')->comment('Total duration from all modules in minutes');
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->integer('total_duration')->default(0)->after('price')->comment('Total duration from all chapters in minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn('estimated_duration');
        });

        Schema::table('chapters', function (Blueprint $table) {
            $table->dropColumn('total_duration');
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn('total_duration');
        });
    }
};
