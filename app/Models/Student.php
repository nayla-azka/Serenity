<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;
    
    protected $table = 'student';
    protected $primaryKey = 'id_student';
    
    protected $fillable = [
        'nis',
        'photo', 
        'student_name',
        'class_id',
        'repeat_grade',
        'user_id'
    ];

    protected $casts = [
        'id_student' => 'integer',
        'class_id' => 'integer',
        'user_id' => 'integer',
        'repeat_grade' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function sessions()
    {
        return $this->hasMany(ChatSession::class, 'id_student', 'id_student');
    }

    public function class()
    {
        return $this->belongsTo(Kelas::class, 'class_id', 'id_class');
    }
}