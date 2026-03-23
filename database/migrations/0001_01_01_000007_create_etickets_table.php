<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etickets', function (Blueprint $table) {
            $table->id();
            // Relasi ke transaksi induk
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            // Relasi ke jenis tiket yang dibeli (VIP/Reguler)
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            // Relasi ke user pemilik tiket
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Kode unik untuk isi QR Code
            $table->string('ticket_code')->unique(); 
            
            // Status validasi (Fitur Scan Simulation)
            $table->boolean('is_scanned')->default(false);
            $table->timestamp('scanned_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etickets');
    }
};