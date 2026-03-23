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
        Schema::table('etickets', function (Blueprint $table) {
            if (!Schema::hasColumn('etickets', 'issued_at')) {
                $table->timestamp('issued_at')->nullable()->after('is_scanned');
            }
            if (!Schema::hasColumn('etickets', 'scanned_at')) {
                $table->timestamp('scanned_at')->nullable()->after('issued_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etickets', function (Blueprint $table) {
            $table->dropColumn(['issued_at', 'scanned_at']);
        });
    }
};