<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ============================================
        // STEP 1: Create new session_views table
        // ============================================
        Schema::create('session_views', function (Blueprint $table) {
            $table->id('id_view');
            $table->unsignedBigInteger('id_session');
            $table->enum('user_type', ['student', 'counselor']);
            $table->unsignedInteger('user_id');
            $table->enum('view_status', ['active', 'archived', 'hidden'])->default('active');
            $table->timestamp('hidden_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            
            $table->unique(['id_session', 'user_type', 'user_id'], 'unique_session_user');
            $table->index(['user_type', 'user_id', 'view_status'], 'idx_user_status');
            
            $table->foreign('id_session', 'fk_views_session')
                  ->references('id_session')
                  ->on('chat_sessions')
                  ->onDelete('cascade');
        });

        // ============================================
        // STEP 2: Migrate existing data to session_views
        // ============================================
        
        // Get all sessions
        $sessions = DB::table('chat_sessions')->get();
        
        foreach ($sessions as $session) {
            // Student view status
            $studentStatus = 'active';
            $studentArchivedAt = null;
            $studentHiddenAt = null;
            
            if ($session->deleted_by_student) {
                $studentStatus = 'hidden';
                $studentHiddenAt = $session->student_deleted_at ?? Carbon::now();
            } elseif ($session->is_archived) {
                $studentStatus = 'archived';
                $studentArchivedAt = $session->archived_at ?? Carbon::now();
            }
            
            // Counselor view status
            $counselorStatus = 'active';
            $counselorArchivedAt = null;
            $counselorHiddenAt = null;
            
            if ($session->deleted_by_counselor) {
                $counselorStatus = 'hidden';
                $counselorHiddenAt = $session->counselor_deleted_at ?? Carbon::now();
            } elseif ($session->archived_by_counselor) {
                $counselorStatus = 'archived';
                $counselorArchivedAt = $session->counselor_archived_at ?? Carbon::now();
            }
            
            // Insert student view
            DB::table('session_views')->insert([
                'id_session' => $session->id_session,
                'user_type' => 'student',
                'user_id' => $session->id_student,
                'view_status' => $studentStatus,
                'archived_at' => $studentArchivedAt,
                'hidden_at' => $studentHiddenAt,
            ]);
            
            // Insert counselor view
            DB::table('session_views')->insert([
                'id_session' => $session->id_session,
                'user_type' => 'counselor',
                'user_id' => $session->id_counselor,
                'view_status' => $counselorStatus,
                'archived_at' => $counselorArchivedAt,
                'hidden_at' => $counselorHiddenAt,
            ]);
        }

        // ============================================
        // STEP 3: Clean up chat_sessions table
        // ============================================
        
        Schema::table('chat_sessions', function (Blueprint $table) {
            // Keep only essential columns
            // Remove all the confusing flags
            $table->dropColumn([
                'is_archived',
                'archived_by_counselor',
                'counselor_archived_at',
                'deleted_by_student',
                'archived_at',
                'deleted_by_counselor',
                'student_deleted_at',
                'counselor_deleted_at',
            ]);
        });

        // ============================================
        // STEP 4: Simplify chat_messages table
        // ============================================
        
Schema::table('chat_messages', function (Blueprint $table) {
    // ✅ Drop the correct constraint by its real name
    $table->dropForeign('chat_messages_ibfk_2');
});


Schema::table('chat_messages', function (Blueprint $table) {
    // Now it's safe to drop columns
    $table->dropColumn([
        'user_id',
        'is_welcome_message',
        'created_timezone'
    ]);
});

// Then modify the status column as before
DB::statement("ALTER TABLE chat_messages MODIFY COLUMN status ENUM('sent', 'read') NOT NULL DEFAULT 'sent'");

        // ============================================
        // STEP 5: Drop old archive tables (optional)
        // ============================================
        
        // If you want to keep old archives accessible:
        // - Keep chat_sessions_archive and chat_messages_archive
        // - Merge them into session_views or keep separate
        
        // If you want a fresh start (recommended):
        // Schema::dropIfExists('chat_sessions_archive');
        // Schema::dropIfExists('chat_messages_archive');
        
        // Better option: Keep them for reference but don't use going forward
        // You can manually clean them up later
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore old columns
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false);
            $table->boolean('archived_by_counselor')->default(false);
            $table->timestamp('counselor_archived_at')->nullable();
            $table->boolean('deleted_by_student')->default(false);
            $table->timestamp('archived_at')->nullable();
            $table->boolean('deleted_by_counselor')->default(false);
            $table->timestamp('student_deleted_at')->nullable();
            $table->timestamp('counselor_deleted_at')->nullable();
        });

        // Restore chat_messages columns
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('is_welcome_message')->default(false);
            $table->string('created_timezone', 100)->nullable();
        });

        // Migrate data back from session_views to old columns
        $views = DB::table('session_views')->get();
        
        foreach ($views as $view) {
            if ($view->user_type === 'student') {
                $updates = [];
                
                if ($view->view_status === 'archived') {
                    $updates['is_archived'] = true;
                    $updates['archived_at'] = $view->archived_at;
                } elseif ($view->view_status === 'hidden') {
                    $updates['deleted_by_student'] = true;
                    $updates['student_deleted_at'] = $view->hidden_at;
                }
                
                if (!empty($updates)) {
                    DB::table('chat_sessions')
                        ->where('id_session', $view->id_session)
                        ->update($updates);
                }
            } elseif ($view->user_type === 'counselor') {
                $updates = [];
                
                if ($view->view_status === 'archived') {
                    $updates['archived_by_counselor'] = true;
                    $updates['counselor_archived_at'] = $view->archived_at;
                } elseif ($view->view_status === 'hidden') {
                    $updates['deleted_by_counselor'] = true;
                    $updates['counselor_deleted_at'] = $view->hidden_at;
                }
                
                if (!empty($updates)) {
                    DB::table('chat_sessions')
                        ->where('id_session', $view->id_session)
                        ->update($updates);
                }
            }
        }

        // Drop session_views table
        Schema::dropIfExists('session_views');
    }
};