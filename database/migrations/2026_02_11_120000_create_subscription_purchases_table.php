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
        Schema::create('subscription_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_code')->unique()->comment('Kode unik untuk purchase subscription, format: SUB-XXXXXX');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('plan', ['standard', 'premium'])->comment('Tipe subscription');
            $table->decimal('amount', 10, 2)->comment('Harga yang dibayar');
            $table->decimal('discount_amount', 10, 2)->default(0)->comment('Jumlah discount');
            $table->decimal('final_amount', 10, 2)->comment('Total harga setelah discount');
            $table->enum('status', ['pending', 'success', 'expired', 'cancelled'])->default('pending')->comment('Status pembayaran');
            $table->string('payment_method')->nullable()->comment('Metode pembayaran (bca-va, gopay, dll)');
            $table->string('payment_provider')->nullable()->comment('Provider pembayaran (whatsapp, dll)');
            $table->timestamp('paid_at')->nullable()->comment('Waktu pembayaran');
            $table->timestamp('expires_at')->nullable()->comment('Waktu kadaluarsa subscription');
            $table->text('notes')->nullable()->comment('Catatan tambahan');
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('plan');
            $table->index('status');
            $table->index('purchase_code');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_purchases');
    }
};
