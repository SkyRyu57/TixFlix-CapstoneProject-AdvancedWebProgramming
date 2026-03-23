<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel events
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            
            $table->string('name'); // Contoh: 'VIP', 'Early Bird', 'Reguler'
            $table->integer('price')->default(0); // Harga tiket (0 berarti gratis)
            $table->integer('stock'); // Kuota tiket
            $table->text('description')->nullable(); // Opsional: misal "Free drink & snack"
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};