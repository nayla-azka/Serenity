<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\Student;
use App\Models\Counselor;
use App\Events\MessageSent;
use App\Events\MessageRead;
use Carbon\Carbon;
use App\Services\NotificationService;

class ChatController extends Controller
{
    // ============================================
    // INDEX - Show main chat interface
    // ============================================
    public function index()
    {
        try {
            $user = Auth::user();

            if (!in_array($user->role, ['counselor', 'konselor'])) {
                return redirect()->back()->with('error', 'Access denied');
            }

            $counselor = Counselor::where('user_id', $user->id)->first();
            if (!$counselor) {
                return redirect()->back()->with('error', 'Counselor profile not found');
            }

            // Get all active sessions for this counselor
            $allSessions = ChatSession::with(['student.user', 'latestMessage'])
                ->activeForCounselor($counselor->id_counselor)
                ->orderBy('updated_at', 'desc')
                ->get();

            foreach ($allSessions as $session) {
                $session->unread_count = $session->getUnreadCountForCounselor();
            }

            $session = null;

            $archivedCount = ChatSession::archivedForCounselor($counselor->id_counselor)->count();
            return view('admin.konseling.index', compact('allSessions', 'session', 'archivedCount'));

        } catch (\Exception $e) {
            Log::error('Error in admin konseling index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    // ============================================
    // SHOW - Show specific session
    // ============================================
    public function show($id_session)
    {
        try {
            $session = ChatSession::with(['student.user', 'counselor'])->find($id_session);

            if (!$session) {
                return redirect()->route('admin.konseling.index')->with('error', 'Session not found');
            }

            $user = Auth::user();

            if (!in_array($user->role, ['counselor', 'konselor'])) {
                return redirect()->route('admin.konseling.index')->with('error', 'Access denied');
            }

            $counselor = Counselor::where('user_id', $user->id)->first();
            if (!$counselor || $counselor->id_counselor !== $session->id_counselor) {
                return redirect()->route('admin.konseling.index')->with('error', 'Unauthorized access');
            }

            // Check if counselor has hidden this session
            $viewStatus = $session->getViewStatus('counselor', $counselor->id_counselor);
            if ($viewStatus === 'hidden') {
                return redirect()->route('admin.konseling.index')->with('error', 'Session not found');
            }

            // Get all sessions for sidebar
            $allSessions = ChatSession::with(['student.user', 'latestMessage'])
                ->activeForCounselor($counselor->id_counselor)
                ->orderBy('updated_at', 'desc')
                ->get();

            foreach ($allSessions as $s) {
                $s->unread_count = $s->getUnreadCountForCounselor();
            }

            return view('admin.konseling.index', compact('session', 'allSessions'));

        } catch (\Exception $e) {
            Log::error('Error showing admin session: ' . $e->getMessage());
            return redirect()->route('admin.konseling.index')->with('error', 'Failed to load session');
        }
    }

    // ============================================
    // GET SESSION STATS
    // ============================================
    public function getSessionStats()
    {
        try {
            $user = Auth::user();

            if (!in_array($user->role, ['counselor', 'konselor'])) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            $counselor = Counselor::where('user_id', $user->id)->first();
            if (!$counselor) {
                return response()->json(['error' => 'Counselor profile not found'], 404);
            }

            $activeSessions = ChatSession::activeForCounselor($counselor->id_counselor)->get();
            $archivedSessions = ChatSession::archivedForCounselor($counselor->id_counselor)->get();

            $totalUnread = 0;
            foreach ($activeSessions as $session) {
                $totalUnread += $session->getUnreadCountForCounselor();
            }

            $stats = [
                'total_sessions' => $activeSessions->count(),
                'active_sessions' => $activeSessions->where('is_active', true)->count(),
                'total_unread' => $totalUnread,
                'archived_sessions' => $archivedSessions->count(),
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            Log::error('Error getting session stats: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get stats'], 500);
        }
    }

    // ============================================
    // DELETE SESSION (Hide from counselor's view)
    // ============================================
    public function deleteSession($id_session)
    {
        try {
            $user = Auth::user();

            if (!in_array($user->role, ['counselor', 'konselor'])) {
                return response()->json(['error' => 'Only counselors can delete sessions'], 403);
            }

            $counselor = Counselor::where('user_id', $user->id)->first();
            if (!$counselor) {
                return response()->json(['error' => 'Counselor profile not found'], 404);
            }

            $session = ChatSession::find($id_session);
            if (!$session || $session->id_counselor !== $counselor->id_counselor) {
                return response()->json(['error' => 'Session not found'], 404);
            }

            // Check if session is active
            if ($session->is_active) {
                return response()->json([
                    'error' => 'Cannot delete active session. Please end the session first.'
                ], 422);
            }

            Log::info("Counselor hiding session {$id_session}");

            // Hide the session from counselor's view
            $session->hideFor('counselor', $counselor->id_counselor);

            $message = 'Session deleted from your view';
            
            // Check if both users have hidden it
            if ($session->isHiddenByBoth()) {
                $message .= ' (Both parties have deleted this session)';
            } else {
                $message .= ' (Student can still access it)';
            }

            Log::info("Session {$id_session} hidden by counselor");
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'hidden_by_both' => $session->isHiddenByBoth()
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting session (counselor): ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Failed to delete session. Please try again.',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ============================================
    // ARCHIVE SESSION
    // ============================================
    public function archiveSession($id_session)
    {
        try {
            $user = Auth::user();

            if (!in_array($user->role, ['counselor', 'konselor'])) {
                return response()->json(['error' => 'Only counselors can archive sessions'], 403);
            }

            $counselor = Counselor::where('user_id', $user->id)->first();
            if (!$counselor) {
                return response()->json(['error' => 'Counselor profile not found'], 404);
            }

            $session = ChatSession::find($id_session);
            if (!$session || $session->id_counselor !== $counselor->id_counselor) {
                return response()->json(['error' => 'Session not found'], 404);
            }

            if ($session->is_active) {
                return response()->json([
                    'error' => 'Cannot archive active session. Please end the session first.'
                ], 422);
            }

            Log::info("Counselor archiving session {$id_session}");

            // Archive the session for counselor
            $session->archiveFor('counselor', $counselor->id_counselor);

            Log::info("Session {$id_session} archived by counselor");

            return response()->json([
                'success' => true,
                'message' => 'Session archived successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error archiving session: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ============================================
    // GET ARCHIVE LIST
    // ============================================
    public function getArchiveList()
{
    try {
        $user = Auth::user();

        if (!in_array($user->role, ['counselor', 'konselor'])) {
            return redirect()->back()->with('error', 'Access denied');
        }

        $counselor = Counselor::where('user_id', $user->id)->first();
        if (!$counselor) {
            return redirect()->back()->with('error', 'Counselor profile not found');
        }

        // Get archived sessions with additional data
        $archivedSessions = DB::table('chat_sessions as cs')
            ->join('session_views as sv', 'cs.id_session', '=', 'sv.id_session')
            ->join('student as s', 'cs.id_student', '=', 's.id_student')
            ->join('users as u', 's.user_id', '=', 'u.id')
            ->leftJoin(DB::raw('(SELECT id_session, COUNT(*) as total_messages, MAX(sent_at) as last_message_at 
                               FROM chat_messages GROUP BY id_session) as msg'), 'cs.id_session', '=', 'msg.id_session')
            ->where('sv.user_type', 'counselor')
            ->where('sv.user_id', $counselor->id_counselor)
            ->where('sv.view_status', 'archived')
            ->select(
                'cs.id_session',
                'cs.id_student',
                'cs.topic',
                'cs.created_at as session_started_at',
                'cs.ended_at as session_ended_at',
                's.student_name',
                'u.name as user_name',
                'sv.archived_at',
                DB::raw('COALESCE(msg.total_messages, 0) as total_messages'),
                'msg.last_message_at'
            )
            ->orderBy('sv.archived_at', 'desc')
            ->get();

        Log::info("Retrieved {$archivedSessions->count()} archived sessions for counselor {$counselor->id_counselor}");

        return view('admin.konseling.archive', compact('archivedSessions'));

    } catch (\Exception $e) {
        Log::error('Error getting archive list: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to load archive');
    }
}


    // ============================================
    // SHOW ARCHIVED SESSION
    // ============================================
   public function showArchivedSession($sessionId)
{
    try {
        $user = Auth::user();
        
        if (!in_array($user->role, ['counselor', 'konselor'])) {
            return redirect()->back()->with('error', 'Access denied');
        }
        
        $counselor = Counselor::where('user_id', $user->id)->first();
        if (!$counselor) {
            return redirect()->back()->with('error', 'Counselor profile not found');
        }
        
        // Load session with relationships
        $session = ChatSession::with(['student.user', 'student.class', 'messages'])->find($sessionId);
        
        if (!$session || $session->id_counselor !== $counselor->id_counselor) {
            return redirect()->back()->with('error', 'Session not found');
        }
        
        // Check if this session is actually archived by counselor
        $viewStatus = $session->getViewStatus('counselor', $counselor->id_counselor);
        if ($viewStatus !== 'archived') {
            return redirect()->route('admin.konseling.index')
                ->with('error', 'This session is not in your archive');
        }
        
        // Get archive record with all the data the view needs
        $archivedSession = DB::table('session_views as sv')
            ->join('chat_sessions as cs', 'sv.id_session', '=', 'cs.id_session')
            ->join('student as s', 'cs.id_student', '=', 's.id_student')
            ->join('users as u', 's.user_id', '=', 'u.id')
            ->where('sv.id_session', $sessionId)
            ->where('sv.user_type', 'counselor')
            ->where('sv.user_id', $counselor->id_counselor)
            ->select(
                'sv.*',
                'cs.created_at as session_started_at',
                'cs.ended_at as session_ended_at',
                'cs.id_student',
                's.student_name',
                'u.name as user_name'
            )
            ->first();
        
        if (!$archivedSession) {
            return redirect()->route('admin.konseling.archive-list')
                ->with('error', 'Archive record not found');
        }
        
        // Attach the student object with class relationship to archivedSession
        // This is needed for the view to access $archivedSession->student->class->class_name
        $archivedSession->student = $session->student;
        
        $messages = $session->messages;
        
        Log::info("Admin viewing archived session {$sessionId}", [
            'counselor_id' => $counselor->id_counselor,
            'message_count' => $messages->count()
        ]);
        
        return view('admin.konseling.archive-show', compact('session', 'messages', 'archivedSession'));
        
    } catch (\Exception $e) {
        Log::error('Error showing archived session: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'session_id' => $sessionId
        ]);
        return redirect()->route('admin.konseling.archive-list')
            ->with('error', 'Failed to load archived session');
    }
}

    // ============================================
    // END SESSION (Only counselor can do this)
    // ============================================
    public function endSession($id_session)
    {
        try {
            $user = Auth::user();

            if (!in_array($user->role, ['counselor', 'konselor'])) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            $session = ChatSession::find($id_session);
            if (!$session) {
                return response()->json(['error' => 'Session not found'], 404);
            }

            $counselor = Counselor::where('user_id', $user->id)->first();
            if (!$counselor || $counselor->id_counselor !== $session->id_counselor) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            if (!$session->canBeEnded()) {
                return response()->json(['error' => 'Session already ended'], 422);
            }

            $session->endSession();

            // Notify student
            $senderName = $counselor->counselor_name ?? 'Counselor';
            $studentUserId = $session->student->user->id ?? null;
            if ($studentUserId) {
                NotificationService::send(
                    $studentUserId,
                    e($senderName) . " has ended the counseling session.",
                    'end_session',
                    [
                        'url' => route('public.konseling.show', $session->id_session),
                        'session_id' => $session->id_session,
                    ]
                );
            }

            return response()->json(['success' => true, 'message' => 'Session ended successfully']);

        } catch (\Exception $e) {
            Log::error('Error ending admin session: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to end session'], 500);
        }
    }

    // ============================================
    // STORE MESSAGE (Send message)
    // ============================================
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_session' => 'required|integer|exists:chat_sessions,id_session',
                'message' => 'required|string|max:1000',
                'timezone' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Not authenticated'], 401);
            }

            if (!in_array($user->role, ['counselor', 'konselor'])) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            $userTimezone = $request->input('timezone', session('timezone', 'UTC'));
            session(['timezone' => $userTimezone]);

            $session = ChatSession::find($request->id_session);
            if (!$session) {
                return response()->json(['error' => 'Session not found'], 404);
            }

            $counselor = Counselor::where('user_id', $user->id)->first();
            if (!$counselor) {
                return response()->json(['error' => 'Counselor profile not found'], 404);
            }

            if ($counselor->id_counselor !== $session->id_counselor) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            if (!$session->is_active) {
                return response()->json(['error' => 'Cannot send messages to ended sessions'], 422);
            }

            // Check if counselor has hidden this session
            $viewStatus = $session->getViewStatus('counselor', $counselor->id_counselor);
            if ($viewStatus === 'hidden') {
                return response()->json(['error' => 'Session not accessible'], 422);
            }

            $senderName = $counselor->counselor_name ?? 'Counselor';
            $nowUtc = Carbon::now('UTC');

            $message = ChatMessage::create([
                'id_session' => $request->id_session,
                'sender_type' => 'counselor',
                'id_sender' => $counselor->id_counselor,
                'message' => $request->message,
                'status' => 'sent',
                'sent_at' => $nowUtc
            ]);

            // Notify student
            $student = Student::find($session->id_student);
            if ($student && $student->user_id) {
                NotificationService::send(
                    $student->user_id,
                    "Konselor {$senderName} mengirimkan pesan baru.",
                    'chat_message',
                    [
                        'session_id' => $session->id_session,
                        'message_id' => $message->id_message,
                        'message_text' => substr($message->message, 0, 100) . (strlen($message->message) > 100 ? '...' : ''),
                        'url' => route('public.konseling.show', $session->id_session),
                    ]
                );
            }

            $displayTime = $nowUtc->copy()->setTimezone($userTimezone);

            $response = [
                'id_message' => $message->id_message,
                'id_session' => $message->id_session,
                'sender_type' => $message->sender_type,
                'id_sender' => $message->id_sender,
                'message' => $message->message,
                'status' => $message->status,
                'sent_at' => $displayTime->format('H:i'),
                'date' => $displayTime->toDateString(),
                'sender_name' => $senderName
            ];

           try {
                event(new MessageSent($message));
                Log::info('Broadcasting admin message', [
                    'message_id' => $message->id_message,
                    'session_id' => $message->id_session,
                    'sender_type' => $message->sender_type
                ]);
            } catch (\Exception $e) {
                Log::warning('Pusher broadcast failed but message was saved: ' . $e->getMessage());
            }

            return response()->json($response, 201);

        } catch (\Exception $e) {
            Log::error('Admin chat message store failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to send message',
                'message' => config('app.debug') ? $e->getMessage() : 'Server error'
            ], 500);
        }
    }

    // ============================================
    // FETCH MESSAGES
    // ============================================
    public function fetchMessages($id_session)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Not authenticated'], 401);
            }

            if (!in_array($user->role, ['counselor', 'konselor'])) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            $userTimezone = session('timezone', 'UTC');

            $session = ChatSession::with(['student.user', 'counselor'])->find($id_session);
            if (!$session) {
                return response()->json(['error' => 'Session not found'], 404);
            }

            $counselor = Counselor::where('user_id', $user->id)->first();
            if (!$counselor || $counselor->id_counselor !== $session->id_counselor) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $messages = ChatMessage::where('id_session', $id_session)
                ->orderBy('sent_at', 'asc')
                ->get();

            $formattedMessages = $messages->map(function($message) use ($session, $userTimezone) {
                $senderName = $message->sender_type === 'student'
                    ? ($session->student->user->name ?? 'Student')
                    : ($session->counselor->counselor_name ?? 'Counselor');

                $messageTime = Carbon::parse($message->sent_at)->setTimezone($userTimezone);

                return [
                    'id_message' => $message->id_message,
                    'id_session' => $message->id_session,
                    'sender_type' => $message->sender_type,
                    'id_sender' => $message->id_sender,
                    'message' => $message->message,
                    'status' => $message->status,
                    'sent_at' => $messageTime->format('H:i'),
                    'date' => $messageTime->toDateString(),
                    'sender_name' => $senderName
                ];
            });

            return response()->json($formattedMessages);

        } catch (\Exception $e) {
            Log::error('Error fetching admin messages: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch messages'], 500);
        }
    }

    // ============================================
    // MARK AS READ
    // ============================================
    public function markAsRead($id_session)
    {
        try {
            $user = Auth::user();

            if (!in_array($user->role, ['counselor', 'konselor'])) {
                return response()->json(['error' => 'Only counselors can mark messages in this view'], 403);
            }

            $session = ChatSession::find($id_session);
            if (!$session) {
                return response()->json(['error' => 'Session not found'], 404);
            }

            $counselor = Counselor::where('user_id', $user->id)->first();
            if (!$counselor || $counselor->id_counselor !== $session->id_counselor) {
                return response()->json(['error' => 'Unauthorized access to this session'], 403);
            }

            // Get unread student messages
            $unreadMessages = ChatMessage::where('id_session', $id_session)
                ->where('sender_type', 'student')
                ->where('status', 'sent')
                ->get();

            if ($unreadMessages->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No unread messages',
                    'updated_count' => 0
                ]);
            }

            $messageIds = $unreadMessages->pluck('id_message')->toArray();

            // Update to read status
            $updatedCount = ChatMessage::where('id_session', $id_session)
                ->where('sender_type', 'student')
                ->where('status', 'sent')
                ->update(['status' => 'read']);

            Log::info('Messages marked as read by counselor', [
                'session_id' => $id_session,
                'updated_count' => $updatedCount,
                'message_ids' => $messageIds
            ]);

            // Broadcast the event
            if ($updatedCount > 0) {
                try {
                    $event = new MessageRead($id_session, $messageIds);
                    broadcast($event);
                    
                    Log::info('✅ BROADCAST SENT - MessageRead event', [
                        'session_id' => $id_session,
                        'message_ids' => $messageIds
                    ]);
                } catch (\Exception $e) {
                    Log::error('❌ BROADCAST FAILED', [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Messages marked as read',
                'updated_count' => $updatedCount,
                'message_ids' => $messageIds
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking messages as read (counselor): ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to mark messages as read',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}