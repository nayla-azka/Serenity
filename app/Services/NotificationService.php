<?php

namespace App\Services;

use App\Events\NotificationSent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationService
{
    public static function send($userId, $message, $type = 'general', $extraData = [])
    {
        // Store in database (persists until user deletes)
        DB::table('notifications')->insert([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\GeneralNotification',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $userId,
            'data' => json_encode([
                'message' => $message,
                'type' => $type,
                ...$extraData
            ]),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Broadcast real-time using your existing event
        event(new NotificationSent('siswa', $userId, $message, $type));
    }

    public static function sendToKonselor($konselorId, $message, $type = 'general', $extraData = [])
    {
        DB::table('notifications')->insert([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\GeneralNotification',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $konselorId,
            'data' => json_encode([
                'message' => $message,
                'type' => $type,
                ...$extraData
            ]),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        event(new NotificationSent('konselor', $konselorId, $message, $type));
    }

    public static function sendToAdmin($message, $type = 'general', $extraData = [])
    {
        $adminUsers = DB::table('users')->where('role', 'admin')->get();
        foreach ($adminUsers as $admin) {
            // Store in database for each admin
            DB::table('notifications')->insert([
                'id' => Str::uuid(),
                'type' => 'App\Notifications\GeneralNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $admin->id,
                'data' => json_encode([
                    'message' => $message,
                    'type' => $type,
                    ...$extraData
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Broadcast to admin channel (for real-time updates)
        event(new NotificationSent('admin', null, $message, $type));
    }
}