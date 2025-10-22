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
        // Create chat_sessions table if it doesn't exist
        if (!Schema::hasTable('chat_sessions')) {
            Schema::create('chat_sessions', function (Blueprint $table) {
                $table->id('id_session');
                $table->unsignedBigInteger('id_student');
                $table->unsignedBigInteger('id_counselor');
                $table->string('topic')->default('General Consultation');
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('id_student')->references('id_student')->on('student')->onDelete('cascade');
                $table->foreign('id_counselor')->references('id_counselor')->on('counselor')->onDelete('cascade');

                $table->index(['id_student', 'is_active']);
                $table->index(['id_counselor', 'is_active']);
            });
            }

        // Create chat_messages table if it doesn't exist
        if (!Schema::hasTable('chat_messages')) {
            Schema::create('chat_messages', function (Blueprint $table) {
                $table->id('id_message');
                $table->unsignedBigInteger('id_session');
                $table->enum('sender_type', ['student', 'counselor']);
                $table->unsignedBigInteger('id_sender');
                $table->text('message');
                $table->enum('status', ['sent', 'delivered', 'read'])->default('sent');
                $table->timestamp('sent_at');

                $table->foreign('id_session')->references('id_session')->on('chat_sessions')->onDelete('cascade');

                $table->index(['id_session', 'sent_at']);
                $table->index(['sender_type', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_sessions');
    }
};
