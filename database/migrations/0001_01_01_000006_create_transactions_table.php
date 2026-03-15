<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // User yang melakukan pembelian
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->integer('total_price');
            
            // Sesuai soal: pending (belum bayar), paid (sudah bayar), failed (gagal)
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            
            // Untuk simpan ID transaksi unik dari sistem kita
            $table->string('reference_number')->unique(); 
            
            // Untuk integrasi payment gateway (misal Midtrans) nanti
            $table->string('snap_token')->nullable(); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};