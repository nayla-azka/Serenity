<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController as AdminProfile;
use App\Http\Controllers\Admin\BannerController as AdminBanner;
use App\Http\Controllers\Admin\ArtikelController as AdminArtikel;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Admin\KonselorController as AdminKonselor;
use App\Http\Controllers\Admin\SiswaController as AdminSiswa;
use App\Http\Controllers\Admin\ReportController as AdminReport;
use App\Http\Controllers\Admin\LaporanController as AdminLaporan;
use App\Http\Controllers\Admin\ChatController as AdminChat;
use App\Http\Controllers\Admin\defaultMessageController as CounselorChat;
use App\Http\Controllers\Public\homeController;
use App\Http\Controllers\Public\PublicProfileController;
use App\Http\Controllers\Public\PublicLoginController;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentRepliesController;
// use App\Http\Controllers\ReplyController;
use App\Http\Controllers\CommentReportController;
use App\Http\Controllers\LikesController;
use App\Http\Controllers\NotificationController;

// routes/web.php
// Route::post('/set-timezone', function (Request $request) {
//     $request->validate(['timezone' => 'required|string|max:50']);

//     // Validate timezone
//     if (in_array($request->timezone, timezone_identifiers_list())) {
//         session(['timezone' => $request->timezone]);

//         return response()->json([
//             'success' => true,
//             'timezone' => $request->timezone
//         ]);
//     }

//     return response()->json([
//         'success' => false,
//         'message' => 'Invalid timezone'
//     ], 422);
// });

Broadcast::routes(['middleware' => ['web', 'auth']]);
Route::post('/broadcasting/auth', function(Request $request) {
    return Broadcast::auth($request);
})->middleware('auth');

Route::get('/test-broadcast/{session_id}', function($session_id) {
    $testMessage = \App\Models\ChatMessage::where('id_session', $session_id)->latest()->first();
    
    if (!$testMessage) {
        return 'No messages found for this session';
    }
    
    try {
        broadcast(new \App\Events\MessageSent($testMessage))->toOthers();
        return 'Broadcast sent! Check console logs.';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
})->middleware('auth');

Route::post('/set-timezone', [App\Http\Controllers\TimezoneController::class, 'setTimezone'])
    ->name('timezone.set');

Route::middleware('auth')->get('/notifications', function () {
    $cacheKey = "notifications:user:" . auth()->id();
    return response()->json(cache()->get($cacheKey, []));
});

/*-----------------------------------
----------------PUBLIC---------------
-----------------------------------*/
Route::get('/debug-welcome', [ChatController::class, 'debugWelcomeMessage']);
Route::get('/debug-sessions', function() {
    try {
        // Check existing sessions
        $sessions = DB::table('chat_sessions')->select('id_session', 'id_student', 'id_counselor', 'is_active')->get();

        // Check a specific message
        $messages = DB::table('chat_messages')
            ->select('id_message', 'id_session', 'is_welcome_message', 'sent_at')
            ->orderBy('id_message', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'sessions' => $sessions,
            'recent_messages' => $messages,
            'total_sessions' => $sessions->count()
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});
// HomePage
Route::get('/', [homeController::class, 'index'])->name('public.index');

// Public Login
Route::get('/serenity/login', [PublicLoginController::class, 'showLogin'])->name('public.login.form');
Route::post('/serenity/login', [PublicLoginController::class, 'login'])->name('public.login');

// ---------------------------
// Public Search Bar
// ---------------------------
Route::get('/search-artikel', [ArtikelController::class, 'search'])->name('artikel.search');


// ---------------------------
// Public Artikel
// ---------------------------
Route::get('/serenity/artikel', [ArtikelController::class, 'index'])->name('public.artikel');
Route::get('/serenity/artikel/{article_id}', [ArtikelController::class, 'show'])->name('public.artikel_show');
// Like toggle route
Route::post('/likes/toggle', [LikesController::class, 'toggle'])
    ->name('likes.toggle')
    ->middleware('auth');

// PUBLIC AUTHENTICATED ROUTES
Route::prefix('serenity')->name('public.')->middleware(['auth'])->group(function () {
    Route::get('/articles/{article_id}/comments/{comment_id}', function ($articleId, $commentId) {
        return redirect()->to(route('public.artikel_show', $articleId) . '#comment-' . $commentId);
    })->name('articles.comment');

    // Public profil
    Route::get('/profile', [PublicProfileController::class, 'index'])->name('profile');

    // Notifications
    Route::get('/notifications/count', [NotificationController::class, 'getUnreadCount']);
    Route::get('/notifications', [NotificationController::class, 'index']);
     Route::post('/notifications/mark-read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/delete', [NotificationController::class, 'delete']);
    Route::delete('/notifications/delete-read', [NotificationController::class, 'deleteAllRead']);
    Route::post('/notifications/test', [NotificationController::class, 'createTestNotification']);

    // Comments
    Route::post('/comments/{article_id}', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment_id}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::get('/comments/{article_id}/load-more', [CommentController::class, 'loadMore'])->name('comments.loadMore');

    // Comment replies
    Route::post('/comment-replies', [CommentRepliesController::class, 'store'])->name('comment-replies.store');
    Route::delete('/comment-replies/{reply_id}', [CommentRepliesController::class, 'destroy'])->name('comment-replies.destroy');
    Route::get('/comment-replies/{comment_id}/load-replies', [CommentRepliesController::class, 'loadReplies'])->name('comment-replies.loadReplies');

    // Public routes (for loading comments without auth)
    Route::get('/comments/{article_id}/load-more', [CommentController::class, 'loadMore'])->name('comments.loadMore');
    Route::get('/comment-replies/{comment_id}/load-replies', [CommentRepliesController::class, 'loadReplies'])->name('comment-replies.loadReplies');

    // Report routes
    Route::post('/comment-reports', [CommentReportController::class, 'store'])
        ->name('comment-reports.store')
        ->middleware('auth');

    

    // Public report
    Route::get('/lapor', [ReportController::class, 'create'])->name('lapor');
    Route::post('/lapor', [ReportController::class, 'store'])->name('lapor.store');

    // FIXED: Student konseling routes
    Route::prefix('konseling')->name('konseling.')->group(function () {

         // Main chat interface
        Route::get('/', [ChatController::class, 'index'])->name('index');

        // Show specific session
        Route::get('/session/{id_session}', [ChatController::class, 'show'])->name('show');

        // Start new session with counselor (preview mode)
        Route::get('/start/{id_counselor}', [ChatController::class, 'startSession'])->name('start');

        // Send message (creates session if needed)
        Route::post('/send', [ChatController::class, 'store']);

        // Fetch messages for session
        Route::get('/fetch/{id_session}', [ChatController::class, 'fetchMessages']);

        // Create new session (for existing sessions that need archiving/deleting)
        Route::post('/new-session', [ChatController::class, 'createNewSession']);

          Route::post('/mark-read/{id_session}', [ChatController::class, 'markAsRead'])->name('mark-read');

        // Delete session (soft delete for student)
        Route::delete('/delete/{id_session}', [ChatController::class, 'deleteSession']);

        // Archive routes
        Route::get('/archive', [ChatController::class, 'viewArchive'])->name('archive-list');
        Route::get('/archive/{sessionId}', [ChatController::class, 'showArchive'])->name('archive.show');
    });
});

Route::post('/serenity/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('public.index');
})->name('public.logout');


/*-----------------------------------
----------------ADMIN----------------
-----------------------------------*/
// --- Admin Login ---
Route::get('/admin', [LoginController::class, 'showLogin'])->name('admin.login.form');
Route::post('/admin/login', [LoginController::class, 'login'])->name('admin.login');

// ADMIN ROUTES
Route::prefix('admin')->middleware(['admin.auth', 'track.visits'])->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/admin/dashboard/data', [DashboardController::class, 'getDashboardData'])->name('admin.dashboard.data');

    // Profile CRUD
    Route::get('profile', [AdminProfile::class, 'index'])->name('profile.index');
    Route::put('profile', [AdminProfile::class, 'update'])->name('profile.update');

    // Banner CRUD
    Route::resource('banner', AdminBanner::class);

    // Artikel CRUD
    Route::resource('artikel', AdminArtikel::class);
    Route::post('artikel/upload', [AdminArtikel::class, 'upload'])->name('artikel.upload');

    // Admin-only routes
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('user', AdminUser::class);
        Route::resource('konselor', AdminKonselor::class);

        Route::prefix('report')->name('report.')->group(function () {
            Route::resource('/', AdminReport::class)->parameters(['' => 'report']);
            Route::post('update-status', [AdminReport::class, 'updateStatus'])->name('updateStatus');
            Route::post('bulk-delete', [AdminReport::class, 'bulkDelete'])->name('bulkDelete');
        });
    });

    // Konselor-only routes
    Route::middleware(['role:konselor'])->group(function () {
        // Siswa CRUD
        Route::resource('siswa', AdminSiswa::class);

        // Counselor settings routes
        Route::get('/pesan/settings', [CounselorChat::class, 'showSettings'])
            ->name('pesan.settings');
        Route::put('/pesan/settings', [CounselorChat::class, 'updateSettings'])
            ->name('pesan.update-settings');

        // Laporan
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::resource('', AdminLaporan::class);
            Route::post('/{id}/status', [AdminLaporan::class, 'updateStatus'])->name('updateStatus');
            Route::post('/bulk-delete', [AdminLaporan::class, 'bulkDelete'])->name('bulkDelete');
            Route::post('/restore', [AdminLaporan::class, 'restore'])->name('restore');
        });

        // FIXED: Konseling (chat) routes
        Route::prefix('konseling')->name('konseling.')->group(function () {
            Route::get('/', [AdminChat::class, 'index'])->name('index');
            Route::get('/show/{id_session}', [AdminChat::class, 'show'])->name('show');
            Route::post('/send', [AdminChat::class, 'store'])->name('send');
            Route::get('/fetch/{id_session}', [AdminChat::class, 'fetchMessages'])->name('fetch');
            Route::post('/end/{id_session}', [AdminChat::class, 'endSession'])->name('end');
            Route::post('/mark-read/{id_session}', [AdminChat::class, 'markAsRead'])->name('mark-read');
            Route::get('/stats', [AdminChat::class, 'getSessionStats'])->name('stats');

           // CORRECTED: Archive routes
             Route::get('/archive-list', [AdminChat::class, 'getArchiveList'])->name('archive-list');
            Route::get('/archive/show/{sessionId}', [AdminChat::class, 'showArchivedSession'])->name('archive.show');
            Route::post('/archive/{id_session}', [AdminChat::class, 'archiveSession'])->name('archive.store');
            Route::delete('/delete/{id_session}', [AdminChat::class, 'deleteSession'])->name('delete');
        });
    });
});

Route::get('/test-read-broadcast/{session_id}', function($session_id) {
    try {
        // Test broadcasting a read receipt
        $testMessageIds = [99, 100, 101]; // Fake message IDs for testing
        
        \Log::info('🧪 TEST: Broadcasting read receipt', [
            'session_id' => $session_id,
            'message_ids' => $testMessageIds
        ]);
        
        $event = new \App\Events\MessageRead($session_id, $testMessageIds);
        broadcast($event);
        
        return response()->json([
            'success' => true,
            'message' => 'Test broadcast sent',
            'session_id' => $session_id,
            'message_ids' => $testMessageIds,
            'channel' => 'private-chat.' . $session_id,
            'event' => 'messages.read',
            'broadcaster' => config('broadcasting.default'),
            'pusher_config' => [
                'app_id' => config('broadcasting.connections.pusher.app_id'),
                'key' => config('broadcasting.connections.pusher.key'),
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
})->middleware('auth');

Route::post('/admin/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('admin.login.form');
})->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Broadcasting Routes (for real-time messaging)
|--------------------------------------------------------------------------
*/

Broadcast::channel('chat.{id_session}', function ($user, $id_session) {
    $session = \App\Models\ChatSession::find($id_session);

    if (!$session) {
        return false;
    }

    // Check if user is the student in this session
    if ($user->role === 'siswa') {
        $student = \App\Models\Student::where('user_id', $user->id)->first();
        return $student && $student->id_student === $session->id_student;
    }

    // Check if user is the counselor in this session (support both role names)
    if ($user->role === 'counselor' || $user->role === 'konselor') {
        $counselor = \App\Models\Counselor::where('user_id', $user->id)->first();
        return $counselor && $counselor->id_counselor === $session->id_counselor;
    }

    return false;
});




if (config('app.debug')) {
    Route::middleware(['auth'])->group(function () {
        // Database diagnostics
        Route::get('/debug/database', [ChatController::class, 'debugDatabase']);

        Route::get('/debug/detailed', function() {
            try {
                $user = Auth::user();

                // Test database connection
                $dbTest = DB::select('SELECT 1 as test');

                // Get table info
                $tables = [
                    'users' => DB::table('users')->count(),
                    'students' => DB::table('students')->count(),
                    'counselors' => DB::table('counselors')->count(),
                    'chat_sessions' => DB::table('chat_sessions')->count(),
                    'chat_messages' => DB::table('chat_messages')->count(),
                ];

                // Check current user's student record
                $studentRecord = null;
                $counselorRecord = null;

                if ($user->role === 'siswa') {
                    $studentRecord = DB::table('students')->where('user_id', $user->id)->first();
                } elseif ($user->role === 'counselor' || $user->role === 'konselor') {
                    $counselorRecord = DB::table('counselors')->where('user_id', $user->id)->first();
                }

                // Check sessions for this user
                $userSessions = [];
                if ($studentRecord) {
                    $userSessions = DB::table('chat_sessions')
                        ->where('id_student', $studentRecord->id_student)
                        ->get();
                } elseif ($counselorRecord) {
                    $userSessions = DB::table('chat_sessions')
                        ->where('id_counselor', $counselorRecord->id_counselor)
                        ->get();
                }

                return response()->json([
                    'database_connection' => 'OK',
                    'database_test' => $dbTest,
                    'table_counts' => $tables,
                    'current_user' => [
                        'id' => $user->id,
                        'role' => $user->role,
                        'email' => $user->email ?? 'N/A'
                    ],
                    'student_record' => $studentRecord,
                    'counselor_record' => $counselorRecord,
                    'user_sessions' => $userSessions,
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'pusher_config' => [
                        'app_key' => env('PUSHER_APP_KEY') ? 'Set' : 'Not Set',
                        'cluster' => env('PUSHER_APP_CLUSTER') ?? 'Not Set',
                        'broadcast_driver' => env('BROADCAST_DRIVER') ?? 'Not Set'
                    ]
                ], 200, [], JSON_PRETTY_PRINT);

            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Database connection failed',
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        });

        Route::post('/debug/simple-message', function(Request $request) {
            Log::info('Simple message test started');

            try {
                // Validate input
                if (!$request->id_session || !$request->message) {
                    return response()->json([
                        'error' => 'Missing required fields',
                        'required' => ['id_session', 'message']
                    ], 400);
                }

                // Get current user
                $user = Auth::user();
                if (!$user) {
                    return response()->json(['error' => 'Not authenticated'], 401);
                }

                // Try to create a simple message
                $data = [
                    'id_session' => (int)$request->id_session,
                    'sender_type' => 'student', // hardcoded for testing
                    'id_sender' => 1, // hardcoded for testing
                    'message' => $request->message,
                    'status' => 'sent',
                    'sent_at' => now()
                ];

                Log::info('Attempting to create message with data:', $data);

                // Try raw DB insert first
                $messageId = DB::table('chat_messages')->insertGetId($data);

                Log::info('Message inserted with ID:', ['id' => $messageId]);

                return response()->json([
                    'success' => true,
                    'message_id' => $messageId,
                    'data' => $data,
                    'user_info' => [
                        'id' => $user->id,
                        'role' => $user->role
                    ]
                ]);

            } catch (\Exception $e) {
                Log::error('Simple message test failed:', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'error' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                ], 500);
            }
        });

        // Test route to check user relationships
        Route::get('/debug/user-info', function() {
            try {
                $user = Auth::user();

                if (!$user) {
                    return response()->json(['error' => 'Not authenticated'], 401);
                }

                $result = [
                    'user' => [
                        'id' => $user->id,
                        'role' => $user->role,
                        'email' => $user->email ?? 'N/A'
                    ],
                    'student_record' => null,
                    'counselor_record' => null
                ];

                if ($user->role === 'siswa') {
                    $result['student_record'] = DB::table('students')
                        ->where('user_id', $user->id)
                        ->first();
                } elseif ($user->role === 'counselor' || $user->role === 'konselor') {
                    $result['counselor_record'] = DB::table('counselors')
                        ->where('user_id', $user->id)
                        ->first();
                }

                return response()->json($result, 200, [], JSON_PRETTY_PRINT);

            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ], 500);
            }
        });
    });
}
