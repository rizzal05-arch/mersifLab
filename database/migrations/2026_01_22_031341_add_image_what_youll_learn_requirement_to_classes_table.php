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
        Schema::table('classes', function (Blueprint $table) {
            if (!Schema::hasColumn('classes', 'image')) {
                $table->string('image')->nullable()->after('description')->comment('Gambar/thumbnail class');
            }
            if (!Schema::hasColumn('classes', 'what_youll_learn')) {
                $table->text('what_youll_learn')->nullable()->after('image')->comment('Apa yang akan dipelajari');
            }
            if (!Schema::hasColumn('classes', 'requirement')) {
                $table->text('requirement')->nullable()->after('what_youll_learn')->comment('Persyaratan untuk mengikuti class');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            if (Schema::hasColumn('classes', 'image')) {
                $table->dropColumn('image');
            }
            if (Schema::hasColumn('classes', 'what_youll_learn')) {
                $table->dropColumn('what_youll_learn');
            }
            if (Schema::hasColumn('classes', 'requirement')) {
                $table->dropColumn('requirement');
            }
        });
    }
};
