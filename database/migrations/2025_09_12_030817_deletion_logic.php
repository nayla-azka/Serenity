<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeletionLogic extends Migration
{
    public function up()
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            // Add columns if they don't exist
            if (!Schema::hasColumn('chat_sessions', 'deleted_by_student')) {
                $table->boolean('deleted_by_student')->default(false);
            }
            if (!Schema::hasColumn('chat_sessions', 'deleted_by_counselor')) {
                $table->boolean('deleted_by_counselor')->default(false);
            }
            if (!Schema::hasColumn('chat_sessions', 'ended_at')) {
                $table->timestamp('ended_at')->nullable();
            }
            if (!Schema::hasColumn('chat_sessions', 'student_deleted_at')) {
                $table->timestamp('student_deleted_at')->nullable();
            }
            if (!Schema::hasColumn('chat_sessions', 'counselor_deleted_at')) {
                $table->timestamp('counselor_deleted_at')->nullable();
            }
        });

        // Add index for auto-cleanup query optimization
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->index(['is_active', 'ended_at'], 'idx_inactive_sessions_cleanup');
            $table->index(['deleted_by_student', 'deleted_by_counselor'], 'idx_deletion_status');
        });
    }

    public function down()
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->dropIndex('idx_inactive_sessions_cleanup');
            $table->dropIndex('idx_deletion_status');
            $table->dropColumn([
                'student_deleted_at',
                'counselor_deleted_at'
            ]);
        });
    }
}