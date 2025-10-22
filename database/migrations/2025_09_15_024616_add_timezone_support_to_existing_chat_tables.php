<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddTimezoneSupportToExistingChatTables extends Migration
{
    /**
     * Run the migrations.
     * This migration works with your existing database structure.
     *
     * @return void
     */
    public function up()
    {
        // Add timezone tracking columns to existing tables
        
        // 1. Add timezone tracking to chat_messages
        Schema::table('chat_messages', function (Blueprint $table) {
            // Add timezone tracking column (optional - for debugging/auditing)
            if (!Schema::hasColumn('chat_messages', 'created_timezone')) {
                $table->string('created_timezone', 100)->nullable()->after('sent_at');
            }
        });

        // 2. Add timezone tracking to chat_messages_archive
        Schema::table('chat_messages_archive', function (Blueprint $table) {
            if (!Schema::hasColumn('chat_messages_archive', 'created_timezone')) {
                $table->string('created_timezone', 100)->nullable()->after('archived_at');
            }
        });

        // 3. Add timezone tracking to chat_sessions_archive
        Schema::table('chat_sessions_archive', function (Blueprint $table) {
            if (!Schema::hasColumn('chat_sessions_archive', 'archived_timezone')) {
                $table->string('archived_timezone', 100)->nullable()->after('archived_at');
            }
        });

        // 4. Fix the sent_at column in chat_messages to not auto-update
        // Your current definition has ON UPDATE current_timestamp() which we don't want
        DB::statement("
            ALTER TABLE chat_messages 
            MODIFY COLUMN sent_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
        ");

        // 5. Same for chat_messages_archive
        DB::statement("
            ALTER TABLE chat_messages_archive 
            MODIFY COLUMN sent_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
        ");

        // 6. Ensure database timezone is set to UTC (for this session)
        DB::statement("SET time_zone = '+00:00'");
        
        // 7. Create indexes for better performance with timezone queries
        Schema::table('chat_messages', function (Blueprint $table) {
            // Index for fetching messages with timezone data
            if (!$this->indexExists('chat_messages', 'chat_messages_session_time_tz_index')) {
                $table->index(['id_session', 'sent_at', 'created_timezone'], 'chat_messages_session_time_tz_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove the timezone columns
        Schema::table('chat_messages', function (Blueprint $table) {
            if (Schema::hasColumn('chat_messages', 'created_timezone')) {
                $table->dropColumn('created_timezone');
            }
            if ($this->indexExists('chat_messages', 'chat_messages_session_time_tz_index')) {
                $table->dropIndex('chat_messages_session_time_tz_index');
            }
        });

        Schema::table('chat_messages_archive', function (Blueprint $table) {
            if (Schema::hasColumn('chat_messages_archive', 'created_timezone')) {
                $table->dropColumn('created_timezone');
            }
        });

        Schema::table('chat_sessions_archive', function (Blueprint $table) {
            if (Schema::hasColumn('chat_sessions_archive', 'archived_timezone')) {
                $table->dropColumn('archived_timezone');
            }
        });

        // Restore original sent_at behavior (if needed)
        DB::statement("
            ALTER TABLE chat_messages 
            MODIFY COLUMN sent_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ");

        DB::statement("
            ALTER TABLE chat_messages_archive 
            MODIFY COLUMN sent_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ");
    }

    /**
     * Check if an index exists
     */
    private function indexExists($table, $indexName)
    {
        $indexes = DB::select("SHOW INDEX FROM {$table}");
        foreach ($indexes as $index) {
            if ($index->Key_name === $indexName) {
                return true;
            }
        }
        return false;
    }
}