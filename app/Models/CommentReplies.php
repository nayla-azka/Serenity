<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommentReplies extends Model
{
    use HasFactory;
    protected $table = 'comment_replies';
    protected $primaryKey = 'reply_id';
    protected $fillable = ['comment_id', 'user_id', 'reply_text', 'is_removed'];

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function likes()
    {
        return $this->morphMany(\App\Models\Like::class, 'target', 'target_type', 'target_id');
    }


}
