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
            // Update status enum to support approval workflow
            $table->dropColumn('status');
        });
        
        Schema::table('classes', function (Blueprint $table) {
            // Add new status enum with approval workflow values
            $table->enum('status', ['draft', 'pending_approval', 'published', 'rejected'])->default('draft')->after('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('classes', function (Blueprint $table) {
            // Restore original status enum
            $table->enum('status', ['active', 'suspended'])->default('active')->after('is_published');
        });
    }
};
