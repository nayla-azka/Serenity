<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class NotificationController extends Controller
{
    // Get all notifications (read and unread together)
    public function index()
    {
        try {
            Log::info('Fetching notifications for user: ' . Auth::id());

            // Get user's timezone from session (set by your layout script)
            $userTimezone = session('timezone', 'UTC');

            // REMOVED LIMIT - Get ALL notifications
            $notifications = DB::table('notifications')
                ->where('notifiable_type', 'App\Models\User')
                ->where('notifiable_id', Auth::id())
                ->orderByRaw('CASE WHEN read_at IS NULL THEN 0 ELSE 1 END ASC') // unread first
                ->orderBy('created_at', 'desc')
                // ->limit(50) // REMOVED THIS LINE
                ->get();

            Log::info('Found ' . $notifications->count() . ' notifications');

            $formattedNotifications = $notifications->map(function ($notification) use ($userTimezone) {
                try {
                    $data = json_decode($notification->data, true) ?? [];
                    
                    // Parse UTC timestamp and convert to user's timezone
                    $dateInUserTz = Carbon::parse($notification->created_at, 'UTC')
                        ->setTimezone($userTimezone);
                    
                    return [
                        'id' => $notification->id,
                        'message' => $data['message'] ?? 'Notifikasi baru',
                        'type' => $data['type'] ?? 'general',
                        'url' => $data['url'] ?? null,
                        'time' => $dateInUserTz->diffForHumans(), // Relative time in user's timezone
                        'time_full' => $dateInUserTz->format('d M Y, H:i'), // Full formatted time
                        'data' => $data,
                        'read' => !is_null($notification->read_at),
                        'isNew' => is_null($notification->read_at)
                    ];
                } catch (Exception $e) {
                    Log::error('Error processing notification ID: ' . $notification->id . ' - ' . $e->getMessage());
                    
                    // Fallback with timezone-aware current time
                    $fallbackTime = Carbon::now('UTC')->setTimezone($userTimezone);
                    
                    return [
                        'id' => $notification->id,
                        'message' => 'Notifikasi baru',
                        'type' => 'general',
                        'url' => null,
                        'time' => 'Baru saja',
                        'time_full' => $fallbackTime->format('d M Y, H:i'),
                        'data' => [],
                        'read' => !is_null($notification->read_at),
                        'isNew' => is_null($notification->read_at)
                    ];
                }
            })->values()->toArray();

            return response()->json([
                'success' => true,
                'data' => $formattedNotifications,
                'count' => count($formattedNotifications),
                'unread_count' => collect($formattedNotifications)->filter(fn($n) => !$n['read'])->count()
            ]);

        } catch (Exception $e) {
            Log::error('Error fetching notifications: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat notifikasi',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error',
                'data' => []
            ], 500);
        }
    }

    // Mark specific notifications as read
    public function markAsRead(Request $request)
    {
        try {
            $request->validate([
                'notification_ids' => 'required|array',
                'notification_ids.*' => 'string'
            ]);

            $notificationIds = $request->input('notification_ids', []);
            
            $updated = DB::table('notifications')
                ->where('notifiable_id', Auth::id())
                ->whereIn('id', $notificationIds)
                ->whereNull('read_at')
                ->update(['read_at' => now(), 'updated_at' => now()]);

            return response()->json([
                'success' => true,
                'status' => 'success',
                'updated' => $updated,
                'message' => 'Notifikasi berhasil ditandai sebagai dibaca'
            ]);
        } catch (Exception $e) {
            Log::error('Error marking notifications as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Gagal menandai notifikasi sebagai dibaca'
            ], 500);
        }
    }

    // Mark all as read
    public function markAllAsRead()
    {
        try {
            $updated = DB::table('notifications')
                ->where('notifiable_id', Auth::id())
                ->whereNull('read_at')
                ->update(['read_at' => now(), 'updated_at' => now()]);

            Log::info("Marked {$updated} notifications as read for user: " . Auth::id());

            return response()->json([
                'success' => true,
                'status' => 'success',
                'updated' => $updated,
                'message' => $updated > 0 
                    ? "Semua {$updated} notifikasi berhasil ditandai sebagai dibaca"
                    : 'Semua notifikasi sudah dibaca'
            ]);
        } catch (Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Gagal menandai semua notifikasi sebagai dibaca'
            ], 500);
        }
    }

    // Delete specific notifications
    public function delete(Request $request)
    {
        try {
            $request->validate([
                'notification_ids' => 'required|array',
                'notification_ids.*' => 'string'
            ]);

            $notificationIds = $request->input('notification_ids', []);
            
            $deleted = DB::table('notifications')
                ->where('notifiable_id', Auth::id())
                ->whereIn('id', $notificationIds)
                ->delete();

            return response()->json([
                'success' => true,
                'status' => 'success',
                'deleted' => $deleted,
                'message' => 'Notifikasi berhasil dihapus'
            ]);
        } catch (Exception $e) {
            Log::error('Error deleting notifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Gagal menghapus notifikasi'
            ], 500);
        }
    }

    // Delete all read notifications
    public function deleteAllRead()
    {
        try {
            $deleted = DB::table('notifications')
                ->where('notifiable_id', Auth::id())
                ->whereNotNull('read_at')
                ->delete();

            Log::info("Deleted {$deleted} read notifications for user: " . Auth::id());

            return response()->json([
                'success' => true,
                'status' => 'success',
                'deleted' => $deleted,
                'message' => $deleted > 0
                    ? "{$deleted} notifikasi yang sudah dibaca berhasil dihapus"
                    : 'Tidak ada notifikasi yang sudah dibaca untuk dihapus'
            ]);
        } catch (Exception $e) {
            Log::error('Error deleting read notifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Gagal menghapus notifikasi yang sudah dibaca'
            ], 500);
        }
    }

    public function getUnreadCount()
    {
        try {
            $count = DB::table('notifications')
                ->where('notifiable_type', 'App\Models\User')
                ->where('notifiable_id', Auth::id())
                ->whereNull('read_at')
                ->count();

            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (Exception $e) {
            Log::error('Error getting unread count: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'count' => 0,
                'message' => 'Gagal menghitung notifikasi yang belum dibaca'
            ], 500);
        }
    }
}