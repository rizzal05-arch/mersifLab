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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_code')->unique()->comment('Kode unik untuk purchase, format: ML-XXXXXX');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->decimal('amount', 10, 2)->comment('Harga yang dibayar');
            $table->enum('status', ['pending', 'success', 'expired', 'cancelled'])->default('success')->comment('Status pembayaran');
            $table->string('payment_method')->nullable()->comment('Metode pembayaran (m-banking, transfer bank, dll)');
            $table->string('payment_provider')->nullable()->comment('Provider pembayaran (03payakan, dll)');
            $table->timestamp('paid_at')->nullable()->comment('Waktu pembayaran');
            $table->text('notes')->nullable()->comment('Catatan tambahan');
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('class_id');
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
        Schema::dropIfExists('purchases');
    }
};
