<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Class (Kursus/Pembelajaran)
     * Relasi: Teacher (1 teacher banyak classes)
     *         Chapter (1 class banyak chapters)
     */
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            
            // Foreign key ke users (teacher)
            $table->foreignId('teacher_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Teacher yang punya class ini');
            
            // Basic info
            $table->string('name')->comment('Nama kelas');
            $table->text('description')->nullable()->comment('Deskripsi kelas');
            $table->string('category')->default('development')->comment('Category of the class');
            
            // Status & visibility
            $table->boolean('is_published')->default(false)->comment('Apakah class sudah published');
            $table->integer('order')->default(0)->comment('Urutan class');
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('teacher_id');
            $table->index('is_published');
            $table->index('order');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
