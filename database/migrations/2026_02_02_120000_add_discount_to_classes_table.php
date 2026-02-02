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
            $table->boolean('has_discount')->default(false)->after('price')->comment('Apakah class memiliki diskon');
            $table->decimal('discount', 10, 2)->nullable()->after('has_discount')->comment('Nominal diskon (Rupiah)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            if (Schema::hasColumn('classes', 'discount')) {
                $table->dropColumn('discount');
            }
            if (Schema::hasColumn('classes', 'has_discount')) {
                $table->dropColumn('has_discount');
            }
        });
    }
};
