<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class ChatMessageArchive extends Model
{
    use HasFactory;
    
    protected $table = 'chat_messages_archive';
    protected $primaryKey = 'id_message_archive';
    public $timestamps = false; // We manage timestamps manually
    
    protected $fillable = [
        'original_message_id',
        'original_session_id', 
        'sender_type', 
        'id_sender', 
        'message', 
        'status', 
        'sent_at',
        'archived_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'archived_at' => 'datetime',
        'original_message_id' => 'integer',
        'original_session_id' => 'integer',
        'id_sender' => 'integer'
    ];

    // Relationships
    public function archivedSession()
    {
        return $this->belongsTo(ChatSessionArchive::class, 'original_session_id', 'original_session_id');
    }

    // Get sender based on sender_type (for archived messages)
    public function sender()
    {
        if ($this->sender_type === 'student') {
            return $this->belongsTo(Student::class, 'id_sender', 'id_student');
        } else {
            return $this->belongsTo(Counselor::class, 'id_sender', 'id_counselor');
        }
    }

    // Accessor for formatted time
    public function getFormattedTimeAttribute()
    {
        return $this->sent_at->format('H:i');
    }

    // Scope for session messages
    public function scopeForArchivedSession($query, $originalSessionId)
    {
        return $query->where('original_session_id', $originalSessionId)
                    ->orderBy('sent_at', 'asc');
    }

    // Scope for messages by sender type
    public function scopeBySenderType($query, $senderType)
    {
        return $query->where('sender_type', $senderType);
    }

    // Scope for messages by specific sender
    public function scopeBySender($query, $senderType, $senderId)
    {
        return $query->where('sender_type', $senderType)
                    ->where('id_sender', $senderId);
    }
}