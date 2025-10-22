<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends BaseModel
{
    protected $table = 'visits';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['user_id', 'ip_address', 'user_agent', 'visited_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
