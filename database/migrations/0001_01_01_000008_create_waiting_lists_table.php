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
        Schema::create('waiting_lists', function (Blueprint $table) {
        $table->id();
        // Siapa yang antre
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        // Antre di event mana
        $table->foreignId('event_id')->constrained()->onDelete('cascade');
        
        // Nomor antrean (Bisa direset per event)
        $table->integer('queue_number'); 
        
        // Status antrean
        // waiting: masih di dalam antrean
        // invited: sudah boleh checkout (stok diamankan sementara buat dia)
        // expired: jatah waktu checkout habis
        // completed: sudah berhasil beli (jadi transaksi)
        $table->enum('status', ['waiting', 'invited', 'expired', 'completed'])->default('waiting');

        $table->timestamp('expires_at')->nullable(); // Batas waktu buat checkout kalau statusnya 'invited'
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waiting_lists');
    }
};
