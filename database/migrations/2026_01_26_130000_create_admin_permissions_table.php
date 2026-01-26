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
        if (!Schema::hasTable('admin_permissions')) {
            Schema::create('admin_permissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('permission'); // e.g., 'manage_courses', 'manage_users', 'view_analytics'
                $table->boolean('granted')->default(true);
                $table->foreignId('granted_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
                
                $table->unique(['user_id', 'permission']);
                $table->index('user_id');
                $table->index('permission');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_permissions');
    }
};
