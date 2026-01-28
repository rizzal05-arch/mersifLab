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
        Schema::table('activity_logs', function (Blueprint $table) {
            // Add subject_type and subject_id for polymorphic relationship
            if (!Schema::hasColumn('activity_logs', 'subject_type')) {
                $table->string('subject_type')->nullable()->after('description');
            }
            if (!Schema::hasColumn('activity_logs', 'subject_id')) {
                $table->unsignedBigInteger('subject_id')->nullable()->after('subject_type');
            }
            
            // Add properties column for storing old/new values (JSON)
            if (!Schema::hasColumn('activity_logs', 'properties')) {
                $table->json('properties')->nullable()->after('subject_id');
            }
            
            // Add index for subject lookup
            if (!Schema::hasIndex('activity_logs', 'activity_logs_subject_index')) {
                $table->index(['subject_type', 'subject_id'], 'activity_logs_subject_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            if (Schema::hasIndex('activity_logs', 'activity_logs_subject_index')) {
                $table->dropIndex('activity_logs_subject_index');
            }
            if (Schema::hasColumn('activity_logs', 'properties')) {
                $table->dropColumn('properties');
            }
            if (Schema::hasColumn('activity_logs', 'subject_id')) {
                $table->dropColumn('subject_id');
            }
            if (Schema::hasColumn('activity_logs', 'subject_type')) {
                $table->dropColumn('subject_type');
            }
        });
    }
};
