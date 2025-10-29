<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleOverview extends Model
{
    protected $table = 'article_overview'; // nama view
    protected $primaryKey = 'article_id'; // karena bukan "id"
    public $timestamps = false; // kalau view tidak bisa update otomatis
}

