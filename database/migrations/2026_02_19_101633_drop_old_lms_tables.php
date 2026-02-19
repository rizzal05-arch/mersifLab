<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('course_student');
        Schema::dropIfExists('videos');
        Schema::dropIfExists('materi');
        Schema::dropIfExists('courses');
    }

    public function down(): void
    {
        // Biarkan kosong kalau memang sudah tidak dipakai lagi
    }
};

