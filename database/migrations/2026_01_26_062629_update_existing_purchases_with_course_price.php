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
     * Update existing purchases to use actual course price instead of default 150000
     */
    public function up(): void
    {
        // Update purchases that have default price (150000) and payment_provider = 'system'
        // to use actual course price
        $purchases = DB::table('purchases')
            ->where('amount', 150000)
            ->where('payment_provider', 'system')
            ->get();
        
        foreach ($purchases as $purchase) {
            // Get course price
            $course = DB::table('classes')->where('id', $purchase->class_id)->first();
            
            if ($course && isset($course->price) && $course->price != 150000) {
                // Update purchase with actual course price
                DB::table('purchases')
                    ->where('id', $purchase->id)
                    ->update([
                        'amount' => $course->price,
                        'updated_at' => now(),
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert purchases back to default price (150000) if needed
        // This is optional, you can leave it empty if you don't want to revert
    }
};
