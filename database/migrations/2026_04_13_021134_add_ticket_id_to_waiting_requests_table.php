<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('waiting_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('waiting_requests', 'ticket_id')) {
                $table->unsignedBigInteger('ticket_id')->nullable()->after('event_id');
                $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('waiting_requests', function (Blueprint $table) {
            $table->dropForeign(['ticket_id']);
            $table->dropColumn('ticket_id');
        });
    }
};