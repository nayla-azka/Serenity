<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Like extends Model
{
    use HasFactory;
    protected $table = 'likes';
    protected $primaryKey = 'like_id';
    protected $fillable = ['user_id', 'target_type', 'target_id'];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Polymorphic relationship
    public function target()
    {
        if ($this->target_type === 'comment') {
            return $this->belongsTo(Comment::class, 'target_id', 'comment_id');
        }
        if ($this->target_type === 'article') {
            return $this->belongsTo(Artikel::class, 'target_id', 'article_id');
        }
        
        return null;
    }

    // Scopes for different target types
    public function scopeForArticles($query)
    {
        return $query->where('target_type', 'article');
    }

    public function scopeForComments($query)
    {
        return $query->where('target_type', 'comment');
    }
}