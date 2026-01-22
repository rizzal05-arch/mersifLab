<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambah kolom duration yang dipakai ClassModel, Chapter, Module.
     */
    public function up(): void
    {
        if (Schema::hasTable('chapters') && !Schema::hasColumn('chapters', 'total_duration')) {
            Schema::table('chapters', function (Blueprint $table) {
                $table->integer('total_duration')->default(0)->after('order')->comment('Total menit (dari modules.estimated_duration)');
            });
        }

        if (Schema::hasTable('classes') && !Schema::hasColumn('classes', 'total_duration')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->integer('total_duration')->default(0)->after('order')->comment('Total menit (dari chapters)');
            });
        }

        if (Schema::hasTable('modules') && !Schema::hasColumn('modules', 'estimated_duration')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->integer('estimated_duration')->default(0)->after('duration')->comment('Durasi estimasi (menit) untuk perhitungan chapter/class');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('chapters') && Schema::hasColumn('chapters', 'total_duration')) {
            Schema::table('chapters', fn (Blueprint $table) => $table->dropColumn('total_duration'));
        }
        if (Schema::hasTable('classes') && Schema::hasColumn('classes', 'total_duration')) {
            Schema::table('classes', fn (Blueprint $table) => $table->dropColumn('total_duration'));
        }
        if (Schema::hasTable('modules') && Schema::hasColumn('modules', 'estimated_duration')) {
            Schema::table('modules', fn (Blueprint $table) => $table->dropColumn('estimated_duration'));
        }
    }
};
