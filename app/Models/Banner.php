<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Banner extends Model
{
    use HasFactory;
    protected $table = 'banner';
    protected $primaryKey = 'id';
    protected $fillable = ['photo', 'title', 'desc'];
}
