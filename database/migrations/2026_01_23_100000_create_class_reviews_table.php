<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Rating & review siswa terhadap kelas (course) yang diikuti.
     */
    public function up(): void
    {
        Schema::create('class_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('rating')->comment('1-5');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['class_id', 'user_id']);
            $table->index('class_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_reviews');
    }
};
