<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tambah approval_status untuk module approval system.
     */
    public function up(): void
    {
        if (Schema::hasTable('modules') && !Schema::hasColumn('modules', 'approval_status')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->enum('approval_status', ['pending_approval', 'approved', 'rejected'])
                    ->default('pending_approval')
                    ->after('is_published')
                    ->comment('Status approval: pending_approval, approved, rejected');
                $table->text('admin_feedback')->nullable()->after('approval_status')->comment('Feedback dari admin saat approve/reject');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('modules') && Schema::hasColumn('modules', 'approval_status')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->dropColumn(['approval_status', 'admin_feedback']);
            });
        }
    }
};
