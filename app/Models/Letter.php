<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Letter extends Model
{
    use HasFactory;
    protected $table = 'parent_summon_letters';
    protected $primaryKey = 'id';
    protected $fillable = ['student_id', 'counselor_id', 'appointment_id', 'reason', 'date_of_issue', 'meeting_date', 'location', 'status'];
}
