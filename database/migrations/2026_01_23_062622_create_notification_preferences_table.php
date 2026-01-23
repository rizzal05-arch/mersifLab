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
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Course & Content Notifications
            $table->boolean('new_course')->default(true);
            $table->boolean('new_chapter')->default(true);
            $table->boolean('new_module')->default(true);
            
            // Teacher Notifications
            $table->boolean('module_approved')->default(true);
            $table->boolean('student_enrolled')->default(true);
            $table->boolean('course_rated')->default(true);
            $table->boolean('course_completed')->default(true);
            
            // Announcements & Promotions
            $table->boolean('announcements')->default(true);
            $table->boolean('promotions')->default(true);
            
            // Learning Updates
            $table->boolean('course_recommendations')->default(true);
            $table->boolean('learning_stats')->default(true);
            
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
