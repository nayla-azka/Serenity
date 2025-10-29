<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add welcome message flag to chat_messages table
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->boolean('is_welcome_message')->default(false)->after('status');
        });
    }

    public function down()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn('is_welcome_message');
        });
    }
};