<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->integer('rating')->check('rating BETWEEN 1 AND 5');
            $table->text('comment')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'event_id']);
        });
        
        // Add columns to events table
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'avg_rating')) {
                $table->decimal('avg_rating', 3, 2)->default(0);
            }
            if (!Schema::hasColumn('events', 'total_reviews')) {
                $table->integer('total_reviews')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['avg_rating', 'total_reviews']);
        });
    }
};