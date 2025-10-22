<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Artikel extends Model
{
    use HasFactory;
    protected $table = 'articles';
    protected $primaryKey = 'article_id';
    protected $fillable = ['photo', 'title', 'content', 'author_id'];

    public function getFirstImageAttribute()
    {
        // cari <img ...> pertama di content
        if (preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $this->content, $match)) {
            return $match['src'];
        }
        return null;
    }

    /**
     * Get only active (non-removed) comments
     */
    public function activeComments()
    {
        return $this->hasMany(Comment::class, 'article_id', 'article_id')
                    ->where('is_removed', 0);
    }

    /**
     * Get the total active comments count attribute
     */
    public function getTotalCommentsAttribute()
    {
        return $this->activeComments()->count();
    }


    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // public function comments()
    // {
    //     return $this->hasMany(Comment::class, 'article_id');
    // }

    // public function likes()
    // {
    //     return $this->hasMany(Like::class, 'target_id')
    //         ->where('target_type', 'article');
    // }
    public function likes()
    {
        return $this->morphMany(Like::class, 'target');
    }

    public function views()
    {
        return $this->hasMany(ArticleView::class, 'article_id');
    }

}
