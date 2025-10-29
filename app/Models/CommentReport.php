<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommentReport extends BaseModel
{
    use HasFactory;
    
    protected $table = 'comment_reports';
    protected $primaryKey = 'report_id';
    protected $fillable = [
        'comment_id', 
        'reported_by', 
        'reason', 
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // Main comment relationship
    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id', 'comment_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Helper to get the target entity that was reported
    public function target()
    {
        // If reply_id is set, this is a reply report
        if ($this->reply_id) {
            return $this->reply;
        }
        // Otherwise, it's a comment report
        return $this->comment;
    }

    // Helper to get the user who created the reported content
    public function targetUser()
    {
        $target = $this->target();
        return $target ? $target->user : null;
    }

    // Helper to determine if this is a reply or comment report
    public function isReplyReport()
    {
        return !is_null($this->reply_id);
    }

    public function isCommentReport()
    {
        return !is_null($this->comment_id) && is_null($this->reply_id);
    }
    
    public function isTargetDeleted(): bool
    {
        return !$this->target();
    }

    // Status scope methods
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'diterima');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'ditolak');
    }

    // Helper methods for status checking
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isAccepted()
    {
        return $this->status === 'diterima';
    }

    public function isRejected()
    {
        return $this->status === 'ditolak';
    }

    public function isReviewed()
    {
        return !is_null($this->reviewed_at);
    }
}