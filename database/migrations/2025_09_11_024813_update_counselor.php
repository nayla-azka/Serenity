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
        Schema::table('counselor', function (Blueprint $table) {
            $table->text('default_chat_message')->nullable()->after('desc');
            $table->boolean('auto_send_welcome')->default(true)->after('default_chat_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('counselor', function (Blueprint $table) {
            $table->dropColumn(['default_chat_message', 'auto_send_welcome']);
        });
    }
};