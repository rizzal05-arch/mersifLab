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
        Schema::table('teacher_withdrawals', function (Blueprint $table) {
            $table->string('transfer_proof')->nullable()->after('admin_notes')->comment('Bukti transfer dari admin');
            $table->string('approval_notes')->nullable()->after('transfer_proof')->comment('Catatan saat approval');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_withdrawals', function (Blueprint $table) {
            $table->dropColumn(['transfer_proof', 'approval_notes']);
        });
    }
};
