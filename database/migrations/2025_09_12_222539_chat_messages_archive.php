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
        Schema::create('chat_messages_archive', function (Blueprint $table) {
            $table->id('id_message_archive');
            $table->unsignedBigInteger('original_message_id'); // Reference to original message ID
            $table->unsignedBigInteger('original_session_id'); // Reference to original session ID
            $table->enum('sender_type', ['student', 'counselor']);
            $table->unsignedBigInteger('id_sender');
            $table->text('message');
            $table->enum('status', ['sent', 'delivered', 'read'])->default('sent');
            $table->timestamp('sent_at');
            $table->timestamp('archived_at')->useCurrent();
            
            // Indexes for better performance
            $table->index('original_session_id');
            $table->index(['original_session_id', 'sent_at']);
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages_archive');
    }
};