<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'chat_messages';
    protected $primaryKey = 'id_message';
    public $timestamps = false; // We use custom sent_at timestamp

    protected $fillable = [
        'id_session',
        'sender_type',
        'id_sender',
        'message',
        'status',
        'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'id_session' => 'integer',
        'id_sender' => 'integer'
    ];

    protected $attributes = [
        'status' => 'sent'
    ];

    // Automatically set sent_at when creating
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->sent_at) {
                $model->sent_at = Carbon::now();
            }
        });
    }

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function session()
    {
        return $this->belongsTo(ChatSession::class, 'id_session', 'id_session');
    }

    public function sender()
    {
        if ($this->sender_type === 'student') {
            return $this->belongsTo(Student::class, 'id_sender', 'id_student');
        } else {
            return $this->belongsTo(Counselor::class, 'id_sender', 'id_counselor');
        }
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    public function getSenderUserAttribute()
    {
        if ($this->sender_type === 'student') {
            $student = Student::find($this->id_sender);
            return $student ? $student->user : null;
        } else {
            $counselor = Counselor::find($this->id_sender);
            return $counselor ? $counselor->user : null;
        }
    }

    public function getSenderNameAttribute()
    {
        if ($this->sender_type === 'student') {
            $user = $this->getSenderUserAttribute();
            return $user ? $user->name : 'Student';
        } else {
            $counselor = Counselor::find($this->id_sender);
            return $counselor ? $counselor->counselor_name : 'Counselor';
        }
    }

    // Accessor for formatted time
    public function getFormattedTimeAttribute()
    {
        return $this->sent_at->format('H:i');
    }

    // Scope for session messages
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('id_session', $sessionId)
                    ->orderBy('sent_at', 'asc');
    }
}