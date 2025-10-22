<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Counselor extends Model
{
    use HasFactory;
    
    protected $table = 'counselor';
    protected $primaryKey = 'id_counselor';
    
    protected $fillable = [
        'nip',
        'counselor_name',
        'photo',
        'kelas',
        'contact',
        'desc',
        'user_id',
        'default_chat_message',
        'auto_send_welcome'
    ];

    protected $casts = [
        'id_counselor' => 'integer',
        'user_id' => 'integer',
        'auto_send_welcome' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function sessions()
    {
        return $this->hasMany(ChatSession::class, 'id_counselor', 'id_counselor');
    }

    /**
     * Get the default welcome message for this counselor
     */
    public function getWelcomeMessage()
    {
        return $this->default_chat_message ?: 
            "Halo! Selamat datang di ruang konseling. Saya {$this->counselor_name}, siap membantu Anda. Bagaimana kabar Anda hari ini? Ada yang bisa saya bantu?";
    }

    /**
     * Check if counselor should automatically send welcome message
     */
    public function shouldSendWelcome()
    {
        return $this->auto_send_welcome;
    }
}