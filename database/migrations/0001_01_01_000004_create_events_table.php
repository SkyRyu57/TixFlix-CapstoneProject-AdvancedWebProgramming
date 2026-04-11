<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('location');
            $table->string('banner')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('status', ['pending', 'published', 'rejected', 'cancelled'])->default('pending');
            $table->bigInteger('views')->default(0);
            $table->decimal('avg_rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('edited_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};