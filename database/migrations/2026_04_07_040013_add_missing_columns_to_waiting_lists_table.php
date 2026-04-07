<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('waiting_lists', function (Blueprint $table) {
            // Cek dan tambah kolom ticket_id jika belum ada
            if (!Schema::hasColumn('waiting_lists', 'ticket_id')) {
                $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            }
            
            // Cek dan tambah kolom quantity jika belum ada
            if (!Schema::hasColumn('waiting_lists', 'quantity')) {
                $table->integer('quantity')->default(1);
            }
            
            // Cek dan tambah kolom notified_at jika belum ada
            if (!Schema::hasColumn('waiting_lists', 'notified_at')) {
                $table->timestamp('notified_at')->nullable();
            }
            
            // Cek dan tambah kolom status jika belum ada
            if (!Schema::hasColumn('waiting_lists', 'status')) {
                $table->enum('status', ['waiting', 'notified', 'expired', 'cancelled'])->default('waiting');
            }
        });
    }

    public function down(): void
    {
        Schema::table('waiting_lists', function (Blueprint $table) {
            $table->dropColumn(['ticket_id', 'quantity', 'notified_at', 'status']);
        });
    }
};