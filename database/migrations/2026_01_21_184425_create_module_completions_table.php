<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabel untuk tracking modules yang sudah completed oleh student
     */
    public function up(): void
    {
        Schema::create('module_completions', function (Blueprint $table) {
            $table->id();
            
            // Foreign key ke modules
            $table->foreignId('module_id')
                ->constrained('modules')
                ->onDelete('cascade');
            
            // Foreign key ke users (student)
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            
            // Foreign key ke class (untuk memudahkan query)
            $table->foreignId('class_id')
                ->constrained('classes')
                ->onDelete('cascade');
            
            // Completion date
            $table->timestamp('completed_at')->useCurrent();
            
            // Unique constraint: 1 student hanya bisa complete 1 module 1x
            $table->unique(['module_id', 'user_id']);
            
            // Timestamps
            $table->timestamps();
            
            // Indexes untuk query speed
            $table->index('module_id');
            $table->index('user_id');
            $table->index('class_id');
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_completions');
    }
};
