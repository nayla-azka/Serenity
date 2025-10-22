<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatSessionArchive extends Model
{
    use HasFactory;
    
    protected $table = 'chat_sessions_archive';
    protected $primaryKey = 'id';
    public $timestamps = false; // We manage timestamps manually
    
    protected $fillable = [
        'original_session_id',
        'id_student',
        'id_counselor',
        'topic',
        'session_started_at',
        'session_ended_at',
        'archived_at',
        'total_messages',
        'last_message_at',
        'archived_by_counselor_id'
    ];

    protected $casts = [
        'session_started_at' => 'datetime',
        'session_ended_at' => 'datetime',
        'archived_at' => 'datetime',
        'last_message_at' => 'datetime',
        'total_messages' => 'integer',
        'original_session_id' => 'integer',
        'id_student' => 'integer',
        'id_counselor' => 'integer',
        'archived_by_counselor_id' => 'integer'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'id_student', 'id_student');
    }

    public function counselor()
    {
        return $this->belongsTo(Counselor::class, 'id_counselor', 'id_counselor');
    }

    public function archivedByCounselor()
    {
        return $this->belongsTo(Counselor::class, 'archived_by_counselor_id', 'id_counselor');
    }

    public function archivedMessages()
    {
        return $this->hasMany(ChatMessageArchive::class, 'original_session_id', 'original_session_id')
                    ->orderBy('sent_at', 'asc');
    }

    // Scopes
    public function scopeForCounselor($query, $counselorId)
    {
        return $query->where('id_counselor', $counselorId);
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('id_student', $studentId);
    }

    public function scopeByTopic($query, $topic)
    {
        return $query->where('topic', 'like', "%{$topic}%");
    }

    // Helper methods
    public function getSessionDurationAttribute()
    {
        if ($this->session_started_at && $this->session_ended_at) {
            return $this->session_started_at->diffInMinutes($this->session_ended_at);
        }
        return 0;
    }

    public function getFormattedSessionDurationAttribute()
    {
        $duration = $this->getSessionDurationAttribute();
        if ($duration < 60) {
            return "{$duration} minutes";
        } else {
            $hours = floor($duration / 60);
            $minutes = $duration % 60;
            return "{$hours}h {$minutes}m";
        }
    }
}