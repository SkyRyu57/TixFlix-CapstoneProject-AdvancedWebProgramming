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
        Schema::create('users', function (Blueprint $table) {
            $table->string('id', 50)->primary(); // VARCHAR(50)
            $table->string('nama', 100);         // nama (bukan name)
            $table->string('email', 100)->unique();
            $table->string('no_telp', 20)->nullable();
            $table->string('role', 20)->nullable();
            $table->text('password');
            $table->timestamp('tgl_dibuat')->nullable();
            $table->timestamp('tgl_diubah')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
