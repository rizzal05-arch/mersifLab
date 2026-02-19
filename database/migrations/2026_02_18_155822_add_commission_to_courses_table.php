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
        Schema::table('courses', function (Blueprint $table) {
            $table->enum('commission_type', ['regular', 'premium'])->default('regular');
            $table->decimal('platform_percentage', 5, 2)->nullable();
            $table->decimal('teacher_percentage', 5, 2)->nullable();
            $table->index('commission_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['commission_type', 'platform_percentage', 'teacher_percentage']);
        });
    }
};
