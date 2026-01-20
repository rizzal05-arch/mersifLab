<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Module (Konten pembelajaran)
     * Types: text, document (PDF), video
     * Relasi: Chapter (1 chapter banyak modules)
     */
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            
            // Foreign key ke chapters
            $table->foreignId('chapter_id')
                ->constrained('chapters')
                ->onDelete('cascade')
                ->comment('Chapter yang punya module ini');
            
            // Basic info
            $table->string('title')->comment('Judul module');
            $table->string('type')->comment('Tipe: text, document, video');
            
            // Content fields (flexible based on type)
            $table->longText('content')->nullable()->comment('Rich text content (untuk type=text)');
            $table->string('file_path')->nullable()->comment('Path ke file (PDF atau Video)');
            $table->string('file_name')->nullable()->comment('Original file name');
            $table->string('video_url')->nullable()->comment('URL video external (YouTube, Vimeo, dll)');
            $table->integer('duration')->nullable()->comment('Video duration dalam detik');
            
            // Metadata
            $table->integer('order')->default(0)->comment('Urutan module dalam chapter');
            $table->boolean('is_published')->default(false)->comment('Apakah module sudah published');
            $table->integer('view_count')->default(0)->comment('Jumlah views');
            
            // File metadata
            $table->string('mime_type')->nullable()->comment('MIME type file');
            $table->integer('file_size')->nullable()->comment('File size dalam bytes');
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('chapter_id');
            $table->index('type');
            $table->index('is_published');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
