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
use App\Http\Controllers\CommentReportController;
use App\Http\Controllers\LikesController;
use App\Http\Controllers\NotificationController;

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

        // Send message (creates session if needed) - ADD NAME
        Route::post('/send', [ChatController::class, 'store'])->name('send'); // ADDED

        // Fetch messages for session - ADD NAME
        Route::get('/fetch/{id_session}', [ChatController::class, 'fetchMessages'])->name('fetch'); // ADDED

        // Create new session (for existing sessions that need archiving/deleting) - ADD NAME
        Route::post('/new-session', [ChatController::class, 'createNewSession'])->name('new-session'); // ADDED

        // Mark messages as read - ADD NAME
        Route::post('/mark-read/{id_session}', [ChatController::class, 'markAsRead'])->name('mark-read'); // ADDED

        // Delete session (soft delete for student) - ADD NAME
        Route::delete('/delete/{id_session}', [ChatController::class, 'deleteSession'])->name('delete'); // ADDED

        // Archive routes
        Route::get('/archive', [ChatController::class, 'viewArchive'])->name('archive-list');
        Route::get('/archive/{sessionId}', [ChatController::class, 'showArchive'])->name('archive.show');
        Route::delete('/archive/{sessionId}', [ChatController::class, 'deleteArchivedSession'])->name('archive.delete');
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

         // Import routes
        Route::get('siswa-import', [AdminSiswa::class, 'showImportForm'])->name('siswa.import');
        Route::post('siswa-import', [AdminSiswa::class, 'import'])->name('siswa.import.process');
        Route::get('siswa-template', [AdminSiswa::class, 'downloadTemplate'])->name('siswa.download.template');
        
        Route::get('/siswa/export/passwords', [AdminSiswa::class, 'exportWithPasswords'])
            ->name('siswa.export.passwords')->middleware('role:konselor,admin');
        Route::get('/siswa/export/all', [AdminSiswa::class, 'exportAllStudents'])
            ->name('siswa.export.all')->middleware('role:konselor,admin');

        // Year Progression Routes
        Route::get('siswa/year-progression', [AdminSiswa::class, 'showYearProgressionPage'])
            ->name('siswa.year-progression');
        Route::post('siswa/year-progression/execute', [AdminSiswa::class, 'executeYearProgression'])
            ->name('siswa.year-progression.execute');

        // Bulk Actions
        Route::post('siswa/bulk-repeat-grade', [AdminSiswa::class, 'bulkUpdateRepeatGrade'])
            ->name('siswa.bulk.repeat');
            
        // Counselor settings routes
        Route::get('/pesan/settings', [CounselorChat::class, 'showSettings'])
            ->name('pesan.settings');
        Route::put('/pesan/settings', [CounselorChat::class, 'updateSettings'])
            ->name('pesan.update-settings');

        // Laporan
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [Adminlaporan::class, 'index'])->name('index');
            Route::get('/{id}/details', [Adminlaporan::class, 'getDetails'])->name('details');
            Route::post('/{id}/update-status', [Adminlaporan::class, 'updateStatus'])->name('updateStatus');
            Route::post('/bulk-delete', [Adminlaporan::class, 'bulkDelete'])->name('bulkDelete');
            Route::post('/restore', [Adminlaporan::class, 'restore'])->name('restore');
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
            
            // Delete route
            Route::delete('/delete/{id_session}', [AdminChat::class, 'deleteSession'])->name('delete');

            // Archive routes - FIXED
            Route::get('/archive', [AdminChat::class, 'getArchiveList'])->name('archive-list');
            Route::get('/archive/{sessionId}', [AdminChat::class, 'showArchivedSession'])->name('archive.show');
            Route::post('/archive/{id_session}', [AdminChat::class, 'archiveSession'])->name('archive');
            Route::delete('/archive/{sessionId}', [AdminChat::class, 'deleteArchivedSession'])->name('archive.delete');
        });
    });
});

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