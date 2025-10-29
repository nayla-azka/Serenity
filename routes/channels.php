<?php
// routes/channels.php

use Illuminate\Support\Facades\Broadcast;
use App\Models\ChatSession;
use App\Models\Student;
use App\Models\Counselor;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private chat channel authorization
Broadcast::channel('chat.{id_session}', function ($user, $id_session) {
    try {
        $session = ChatSession::find($id_session);

        if (!$session) {
            return false;
        }

        // Check if user is the student in this session
        if ($user->role === 'siswa') {
            $student = Student::where('user_id', $user->id)->first();
            return $student && $student->id_student === $session->id_student;
        }

        // Check if user is the counselor in this session (support both role names)
        if ($user->role === 'counselor' || $user->role === 'konselor') {
            $counselor = Counselor::where('user_id', $user->id)->first();
            return $counselor && $counselor->id_counselor === $session->id_counselor;
        }

        return false;
    } catch (\Exception $e) {
        \Log::error('Channel authorization error: ' . $e->getMessage());
        return false;
    }
});

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id && $user->role === 'siswa';
});

Broadcast::channel('konselor.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id && $user->role === 'konselor';
});

Broadcast::channel('admin', function ($user) {
    return $user->role === 'admin';
});