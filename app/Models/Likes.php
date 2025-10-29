<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Likes extends Model
{
    use HasFactory;
    protected $table = 'likes';
    protected $primaryKey = 'like_id';
    protected $fillable = ['user_id', 'target_type', 'target_id'];
}
