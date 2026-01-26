<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Migration ini mengisi purchase history dari data enrollment yang sudah ada
     * di tabel class_student atau course_student
     */
    public function up(): void
    {
        // Cek apakah tabel class_student ada
        if (Schema::hasTable('class_student')) {
            $enrollments = DB::table('class_student')
                ->select('user_id', 'class_id', 'enrolled_at', 'created_at')
                ->get();
            
            foreach ($enrollments as $enrollment) {
                // Cek apakah purchase sudah ada untuk enrollment ini
                $existingPurchase = DB::table('purchases')
                    ->where('user_id', $enrollment->user_id)
                    ->where('class_id', $enrollment->class_id)
                    ->first();
                
                // Jika belum ada, buat purchase record
                if (!$existingPurchase) {
                    DB::table('purchases')->insert([
                        'purchase_code' => $this->generatePurchaseCode(),
                        'user_id' => $enrollment->user_id,
                        'class_id' => $enrollment->class_id,
                        'amount' => 150000, // Default price
                        'status' => 'success',
                        'payment_method' => 'checkout',
                        'payment_provider' => 'system',
                        'paid_at' => $enrollment->enrolled_at ?? $enrollment->created_at ?? now(),
                        'created_at' => $enrollment->created_at ?? now(),
                        'updated_at' => $enrollment->created_at ?? now(),
                    ]);
                }
            }
        }
        
        // Cek juga tabel course_student jika ada (untuk backward compatibility)
        if (Schema::hasTable('course_student')) {
            $enrollments = DB::table('course_student')
                ->select('user_id', 'course_id as class_id', 'enrolled_at', 'created_at')
                ->get();
            
            foreach ($enrollments as $enrollment) {
                // Cek apakah purchase sudah ada untuk enrollment ini
                $existingPurchase = DB::table('purchases')
                    ->where('user_id', $enrollment->user_id)
                    ->where('class_id', $enrollment->class_id)
                    ->first();
                
                // Jika belum ada, buat purchase record
                if (!$existingPurchase) {
                    DB::table('purchases')->insert([
                        'purchase_code' => $this->generatePurchaseCode(),
                        'user_id' => $enrollment->user_id,
                        'class_id' => $enrollment->class_id,
                        'amount' => 150000, // Default price
                        'status' => 'success',
                        'payment_method' => 'checkout',
                        'payment_provider' => 'system',
                        'paid_at' => $enrollment->enrolled_at ?? $enrollment->created_at ?? now(),
                        'created_at' => $enrollment->created_at ?? now(),
                        'updated_at' => $enrollment->created_at ?? now(),
                    ]);
                }
            }
        }
    }

    /**
     * Generate unique purchase code
     */
    private function generatePurchaseCode(): string
    {
        do {
            $code = 'ML-' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (DB::table('purchases')->where('purchase_code', $code)->exists());
        
        return $code;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus purchase yang dibuat dari migration ini (yang payment_provider = 'system')
        DB::table('purchases')
            ->where('payment_provider', 'system')
            ->where('payment_method', 'checkout')
            ->delete();
    }
};
