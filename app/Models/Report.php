<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends BaseModel
{
    use HasFactory;
    protected $table = 'reports';
    protected $primaryKey = 'id';
    protected $fillable = ['sender_id', 'topic', 'date', 'place', 'chronology', 'status', 'created_at', 'updated_at'];

    protected $casts = [
        'date' => 'date',        // or 'datetime' depending on column type
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

}
