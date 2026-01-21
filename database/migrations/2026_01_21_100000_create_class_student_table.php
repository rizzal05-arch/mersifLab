<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Migration ini membuat relasi many-to-many antara:
     * - Classes (courses)
     * - Users (students)
     * 
     * Berguna untuk tracking student yang enroll di class mana saja.
     */
    public function up(): void
    {
        Schema::create('class_student', function (Blueprint $table) {
            $table->id();
            
            // Foreign key ke classes
            $table->foreignId('class_id')
                ->constrained('classes')
                ->onDelete('cascade');
            
            // Foreign key ke users (student)
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            
            // Enrollment date
            $table->timestamp('enrolled_at')->useCurrent();
            
            // Progress tracking
            $table->decimal('progress', 5, 2)->default(0)->comment('Progress 0-100%');
            $table->timestamp('completed_at')->nullable();
            
            // Unique constraint: 1 student hanya bisa enroll 1 class 1x
            $table->unique(['class_id', 'user_id']);
            
            // Timestamps for updated_at
            $table->timestamps();
            
            // Indexes untuk query speed
            $table->index('class_id');
            $table->index('user_id');
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_student');
    }
};
