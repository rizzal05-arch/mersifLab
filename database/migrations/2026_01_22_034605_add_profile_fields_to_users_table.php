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
        Schema::table('users', function (Blueprint $table) {
            // Fields for teacher profile
            $table->text('bio')->nullable()->after('email');
            $table->string('phone')->nullable()->after('bio');
            $table->text('address')->nullable()->after('phone');
            
            // Fields for student profile (alternative names)
            $table->string('telephone')->nullable()->after('address');
            $table->text('biography')->nullable()->after('telephone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bio', 'phone', 'address', 'telephone', 'biography']);
        });
    }
};
