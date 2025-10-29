<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends BaseModel
{
    use HasFactory;
    protected $table = 'comments';
    protected $primaryKey = 'comment_id';
    protected $fillable = ['article_id', 'user_id', 'comment_text', 'is_removed', 'parent_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
    public function article()
    {
        return $this->belongsTo(Artikel::class, 'article_id');
    }
     
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id', 'comment_id');
    }
    
    // Updated to only count active replies
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id', 'comment_id')
                    ->where('is_removed', 0)
                    ->latest();
    }
    
    // Count only active replies
    public function activeReplies()
    {
        return $this->hasMany(Comment::class, 'parent_id', 'comment_id')
                    ->where('is_removed', 0);
    }

    public function allReplies()
{
    return $this->hasMany(Comment::class, 'parent_id', 'comment_id')
                ->latest();
}

public function allNestedReplies()
{
    return $this->allReplies()->with('allNestedReplies');
}
    
    public function nestedReplies()
    {
        return $this->replies()->with('nestedReplies');
    }
    
    public function likes()
    {
        return $this->hasMany(Like::class, 'target_id', 'comment_id')
                    ->where('target_type', 'comment');
    }

    public function isParent()
    {
        return $this->activeReplies()->exists();
    }
    
    public function isReply()
    {
        return !is_null($this->parent_id);
    }

    // Get only top-level comments (no parent) that are active
    public function scopeParentComments($query)
    {
        return $query->whereNull('parent_id')->where('is_removed', 0);
    }

    // Get only replies (has parent) that are active
    public function scopeReplies($query)
    {
        return $query->whereNotNull('parent_id')->where('is_removed', 0);
    }

    // Scope for non-removed comments
    public function scopeActive($query)
    {
        return $query->where('is_removed', 0);
    }
}