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
            $table->dateTime('discount_starts_at')->nullable()->after('discount')->comment('Mulai periode diskon');
            $table->dateTime('discount_ends_at')->nullable()->after('discount_starts_at')->comment('Berakhir periode diskon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            if (Schema::hasColumn('classes', 'discount_ends_at')) {
                $table->dropColumn('discount_ends_at');
            }
            if (Schema::hasColumn('classes', 'discount_starts_at')) {
                $table->dropColumn('discount_starts_at');
            }
        });
    }
};
