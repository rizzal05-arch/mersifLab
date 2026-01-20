<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pastikan semua data role VALID sebelum ubah ENUM
        DB::statement("
            UPDATE users 
            SET role = 'student'
            WHERE role IS NULL 
               OR role NOT IN ('admin', 'teacher', 'student')
        ");

        // 2. Baru ubah ENUM
        DB::statement("
            ALTER TABLE users 
            MODIFY role ENUM('admin', 'teacher', 'student') 
            DEFAULT 'student'
        ");
    }

    public function down(): void
    {
        // rollback aman (kembalikan ke enum lama)
        DB::statement("
            UPDATE users 
            SET role = 'user'
            WHERE role NOT IN ('admin', 'user')
        ");

        DB::statement("
            ALTER TABLE users 
            MODIFY role ENUM('admin', 'user') 
            DEFAULT 'user'
        ");
    }
};
