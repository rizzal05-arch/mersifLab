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
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('action')->comment('Action yang dilakukan (e.g., registered, created_course)');
                $table->text('description')->comment('Deskripsi lengkap aktivitas');
                $table->timestamps();
                
                $table->index('user_id');
                $table->index('created_at');
            });
        } else {
            // If table exists, check and add missing columns
            Schema::table('activity_logs', function (Blueprint $table) {
                if (!Schema::hasColumn('activity_logs', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->after('id');
                }
                if (!Schema::hasColumn('activity_logs', 'action')) {
                    $table->string('action')->comment('Action yang dilakukan (e.g., registered, created_course)')->after('user_id');
                }
                if (!Schema::hasColumn('activity_logs', 'description')) {
                    $table->text('description')->comment('Deskripsi lengkap aktivitas')->after('action');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
