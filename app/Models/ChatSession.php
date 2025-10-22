<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ChatSession extends Model
{
    use HasFactory;

    protected $table = 'chat_sessions';
    protected $primaryKey = 'id_session';

    protected $fillable = [
        'id_student',
        'id_counselor',
        'topic',
        'is_active',
        'ended_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'ended_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function student()
    {
        return $this->belongsTo(Student::class, 'id_student', 'id_student');
    }

    public function counselor()
    {
        return $this->belongsTo(Counselor::class, 'id_counselor', 'id_counselor');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'id_session', 'id_session')
                    ->orderBy('sent_at', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class, 'id_session', 'id_session')
                    ->orderBy('sent_at', 'desc');
    }

    // ============================================
    // VIEW MANAGEMENT (Independent per user)
    // ============================================

    /**
     * Get user's view status for this session
     */
    public function getViewStatus($userType, $userId)
    {
        $view = DB::table('session_views')
            ->where('id_session', $this->id_session)
            ->where('user_type', $userType)
            ->where('user_id', $userId)
            ->first();

        return $view ? $view->view_status : 'active';
    }

    /**
     * Set user's view status (archive, hide, or restore to active)
     */
    public function setViewStatus($userType, $userId, $status)
    {
        $data = [
            'view_status' => $status,
        ];

        if ($status === 'archived') {
            $data['archived_at'] = Carbon::now();
            $data['hidden_at'] = null;
        } elseif ($status === 'hidden') {
            $data['hidden_at'] = Carbon::now();
        } elseif ($status === 'active') {
            $data['archived_at'] = null;
            $data['hidden_at'] = null;
        }

        DB::table('session_views')->updateOrInsert(
            [
                'id_session' => $this->id_session,
                'user_type' => $userType,
                'user_id' => $userId
            ],
            $data
        );

        Log::info("Session {$this->id_session} view updated", [
            'user_type' => $userType,
            'user_id' => $userId,
            'status' => $status
        ]);
    }

    /**
     * Archive session for a specific user
     */
    public function archiveFor($userType, $userId)
    {
        $this->setViewStatus($userType, $userId, 'archived');
    }

    /**
     * Hide (delete) session for a specific user
     */
    public function hideFor($userType, $userId)
    {
        $this->setViewStatus($userType, $userId, 'hidden');
    }

    /**
     * Restore session to active view for a specific user
     */
    public function restoreFor($userType, $userId)
    {
        $this->setViewStatus($userType, $userId, 'active');
    }

    /**
     * Check if both users have hidden the session (safe to permanently delete)
     */
    public function isHiddenByBoth()
    {
        $studentView = $this->getViewStatus('student', $this->id_student);
        $counselorView = $this->getViewStatus('counselor', $this->id_counselor);

        return $studentView === 'hidden' && $counselorView === 'hidden';
    }

    // ============================================
    // SESSION STATUS MANAGEMENT
    // ============================================

    /**
     * End session (only counselor can do this)
     */
    public function endSession()
    {
        $this->update([
            'is_active' => false,
            'ended_at' => Carbon::now()
        ]);

        Log::info("Session {$this->id_session} ended");
        return true;
    }

    /**
     * Check if session can be ended
     */
    public function canBeEnded()
    {
        return $this->is_active;
    }

    // ============================================
    // QUERY SCOPES
    // ============================================

    /**
     * Get sessions visible to student (not hidden)
     */
    public function scopeVisibleToStudent($query, $studentId)
    {
        return $query->where('id_student', $studentId)
            ->whereExists(function($q) use ($studentId) {
                $q->select(DB::raw(1))
                  ->from('session_views')
                  ->whereColumn('session_views.id_session', 'chat_sessions.id_session')
                  ->where('session_views.user_type', 'student')
                  ->where('session_views.user_id', $studentId)
                  ->whereIn('session_views.view_status', ['active', 'archived']);
            });
    }

    /**
     * Get sessions in student's active list (not archived, not hidden)
     */
    public function scopeActiveForStudent($query, $studentId)
    {
        return $query->where('id_student', $studentId)
            ->where(function($q) use ($studentId) {
                // Either no view record exists (defaults to active)
                $q->whereNotExists(function($subq) use ($studentId) {
                    $subq->select(DB::raw(1))
                        ->from('session_views')
                        ->whereColumn('session_views.id_session', 'chat_sessions.id_session')
                        ->where('session_views.user_type', 'student')
                        ->where('session_views.user_id', $studentId);
                })
                // Or view exists and is 'active'
                ->orWhereExists(function($subq) use ($studentId) {
                    $subq->select(DB::raw(1))
                        ->from('session_views')
                        ->whereColumn('session_views.id_session', 'chat_sessions.id_session')
                        ->where('session_views.user_type', 'student')
                        ->where('session_views.user_id', $studentId)
                        ->where('session_views.view_status', 'active');
                });
            });
    }

    /**
     * Get sessions in student's archive
     */
    public function scopeArchivedForStudent($query, $studentId)
    {
        return $query->where('id_student', $studentId)
            ->whereExists(function($q) use ($studentId) {
                $q->select(DB::raw(1))
                  ->from('session_views')
                  ->whereColumn('session_views.id_session', 'chat_sessions.id_session')
                  ->where('session_views.user_type', 'student')
                  ->where('session_views.user_id', $studentId)
                  ->where('session_views.view_status', 'archived');
            });
    }

    /**
     * Get sessions visible to counselor (not hidden)
     */
    public function scopeVisibleToCounselor($query, $counselorId)
    {
        return $query->where('id_counselor', $counselorId)
            ->whereExists(function($q) use ($counselorId) {
                $q->select(DB::raw(1))
                  ->from('session_views')
                  ->whereColumn('session_views.id_session', 'chat_sessions.id_session')
                  ->where('session_views.user_type', 'counselor')
                  ->where('session_views.user_id', $counselorId)
                  ->whereIn('session_views.view_status', ['active', 'archived']);
            });
    }

    /**
     * Get sessions in counselor's active list
     */
    public function scopeActiveForCounselor($query, $counselorId)
    {
        return $query->where('id_counselor', $counselorId)
            ->where(function($q) use ($counselorId) {
                $q->whereNotExists(function($subq) use ($counselorId) {
                    $subq->select(DB::raw(1))
                        ->from('session_views')
                        ->whereColumn('session_views.id_session', 'chat_sessions.id_session')
                        ->where('session_views.user_type', 'counselor')
                        ->where('session_views.user_id', $counselorId);
                })->orWhereExists(function($subq) use ($counselorId) {
                    $subq->select(DB::raw(1))
                        ->from('session_views')
                        ->whereColumn('session_views.id_session', 'chat_sessions.id_session')
                        ->where('session_views.user_type', 'counselor')
                        ->where('session_views.user_id', $counselorId)
                        ->where('session_views.view_status', 'active');
                });
            });
    }

    /**
     * Get sessions in counselor's archive
     */
    public function scopeArchivedForCounselor($query, $counselorId)
    {
        return $query->where('id_counselor', $counselorId)
            ->whereExists(function($q) use ($counselorId) {
                $q->select(DB::raw(1))
                  ->from('session_views')
                  ->whereColumn('session_views.id_session', 'chat_sessions.id_session')
                  ->where('session_views.user_type', 'counselor')
                  ->where('session_views.user_id', $counselorId)
                  ->where('session_views.view_status', 'archived');
            });
    }

    // ============================================
    // UNREAD COUNTS
    // ============================================

    public function getUnreadCountForStudent()
    {
        return ChatMessage::where('id_session', $this->id_session)
            ->where('sender_type', 'counselor')
            ->where('status', 'sent')
            ->count();
    }

    public function getUnreadCountForCounselor()
    {
        return ChatMessage::where('id_session', $this->id_session)
            ->where('sender_type', 'student')
            ->where('status', 'sent')
            ->count();
    }

    // ============================================
    // CLEANUP (Optional - for cron job)
    // ============================================

    /**
     * Permanently delete sessions hidden by both users and older than X days
     */
    public static function cleanupOldHiddenSessions($daysOld = 90)
    {
        $cutoffDate = Carbon::now()->subDays($daysOld);

        $sessions = self::where('ended_at', '<', $cutoffDate)
            ->where('is_active', false)
            ->get();

        $deleted = 0;
        foreach ($sessions as $session) {
            if ($session->isHiddenByBoth()) {
                $session->delete(); // Cascade deletes messages
                $deleted++;
            }
        }

        Log::info("Cleaned up {$deleted} old hidden sessions");
        return $deleted;
    }
}