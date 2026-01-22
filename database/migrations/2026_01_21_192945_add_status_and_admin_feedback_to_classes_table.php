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
        Schema::table('classes', function (Blueprint $table) {
            if (!Schema::hasColumn('classes', 'title')) {
                $table->string('title')->default('')->after('name')->comment('Judul class');
            }
            if (!Schema::hasColumn('classes', 'status')) {
                $table->enum('status', ['active', 'suspended'])->default('active')->after('is_published');
            }
            if (!Schema::hasColumn('classes', 'admin_feedback')) {
                $table->text('admin_feedback')->nullable()->after('status');
            }
            if (!Schema::hasColumn('classes', 'price')) {
                $table->decimal('price', 10, 2)->default(0)->after('description');
            }
            if (!Schema::hasColumn('classes', 'total_sales')) {
                $table->integer('total_sales')->default(0)->after('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            if (Schema::hasColumn('classes', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('classes', 'admin_feedback')) {
                $table->dropColumn('admin_feedback');
            }
            if (Schema::hasColumn('classes', 'price')) {
                $table->dropColumn('price');
            }
            if (Schema::hasColumn('classes', 'total_sales')) {
                $table->dropColumn('total_sales');
            }
        });
    }
};
