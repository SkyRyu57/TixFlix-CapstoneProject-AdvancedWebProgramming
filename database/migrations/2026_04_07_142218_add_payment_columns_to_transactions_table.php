<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Tambahkan kolom yang diperlukan untuk payment QR code
            if (!Schema::hasColumn('transactions', 'order_id')) {
                $table->string('order_id')->unique()->nullable()->after('id');
            }
            if (!Schema::hasColumn('transactions', 'ticket_id')) {
                $table->foreignId('ticket_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('transactions', 'event_id')) {
                $table->foreignId('event_id')->nullable()->after('ticket_id');
            }
            if (!Schema::hasColumn('transactions', 'quantity')) {
                $table->integer('quantity')->default(1)->after('total_price');
            }
            if (!Schema::hasColumn('transactions', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'order_id', 'ticket_id', 'event_id', 'quantity', 'expires_at'
            ]);
        });
    }
};