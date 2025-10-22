<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArticleView extends Model
{
    use HasFactory;

    protected $table = 'article_views';
    protected $primaryKey = 'view_id';
    public $timestamps = true; // since you have created_at

    protected $fillable = [
        'article_id',
        'user_id',
        'ip_address',
    ];

    /**
     * Each view belongs to an article.
     */
    public function article()
    {
        return $this->belongsTo(Artikel::class, 'article_id', 'article_id');
    }

    /**
     * Each view may belong to a user (nullable).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
