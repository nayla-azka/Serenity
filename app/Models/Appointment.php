<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;
    protected $table = 'counseling_appointments';
    protected $primaryKey = 'id';
    protected $fillable = ['student_id', 'counselor_id', 'topic', 'description', 'preferred_time', 'status', 'scheduled_time'];
}
