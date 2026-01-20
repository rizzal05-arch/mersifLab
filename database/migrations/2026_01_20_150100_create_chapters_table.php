<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Chapter (Bab/Bagian dalam class)
     * Relasi: Class (1 class banyak chapters)
     *         Module (1 chapter banyak modules)
     */
    public function up(): void
    {
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            
            // Foreign key ke classes
            $table->foreignId('class_id')
                ->constrained('classes')
                ->onDelete('cascade')
                ->comment('Class yang punya chapter ini');
            
            // Basic info
            $table->string('title')->comment('Judul chapter');
            $table->text('description')->nullable()->comment('Deskripsi chapter');
            
            // Status & ordering
            $table->boolean('is_published')->default(false)->comment('Apakah chapter sudah published');
            $table->integer('order')->default(0)->comment('Urutan chapter dalam class');
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('class_id');
            $table->index('is_published');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
