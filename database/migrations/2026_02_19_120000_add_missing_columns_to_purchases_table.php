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
        Schema::table('purchases', function (Blueprint $table) {
            // Add missing columns if they don't already exist
            if (!Schema::hasColumn('purchases', 'platform_commission')) {
                $table->decimal('platform_commission', 10, 2)->nullable()->default(0)->comment('Komisi platform');
            }
            if (!Schema::hasColumn('purchases', 'teacher_earning')) {
                $table->decimal('teacher_earning', 10, 2)->nullable()->comment('Pendapatan guru setelah komisi');
            }
            if (!Schema::hasColumn('purchases', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->comment('Waktu persetujuan pembayaran');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumnIfExists('platform_commission');
            $table->dropColumnIfExists('teacher_earning');
            $table->dropColumnIfExists('approved_at');
        });
    }
};
