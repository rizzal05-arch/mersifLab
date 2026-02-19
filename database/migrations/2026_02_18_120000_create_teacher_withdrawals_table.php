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
        Schema::create('teacher_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2)->comment('Jumlah penarikan');
            $table->enum('status', ['pending', 'approved', 'rejected', 'processed'])->default('pending')->comment('Status penarikan');
            $table->text('notes')->nullable()->comment('Catatan dari teacher atau admin');
            $table->text('admin_notes')->nullable()->comment('Catatan dari admin');
            $table->string('withdrawal_code')->unique()->comment('Kode unik untuk penarikan, format: WD-XXXXXX');
            $table->string('bank_name')->nullable()->comment('Nama bank');
            $table->string('bank_account_name')->nullable()->comment('Nama pemilik rekening');
            $table->string('bank_account_number')->nullable()->comment('Nomor rekening');
            $table->timestamp('requested_at')->comment('Waktu pengajuan penarikan');
            $table->timestamp('processed_at')->nullable()->comment('Waktu proses penarikan');
            $table->timestamps();
            
            // Indexes
            $table->index('teacher_id');
            $table->index('status');
            $table->index('withdrawal_code');
            $table->index('requested_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_withdrawals');
    }
};
