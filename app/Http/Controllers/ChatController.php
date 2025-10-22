<?php

namespace App\Http\Controllers;

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
            $counselors = Counselor::all();

            if ($user->role === 'siswa') {
                $student = Student::where('user_id', $user->id)->first();
                if (!$student) {
                    return redirect()->back()->with('error', 'Student profile not found');
                }

                // Get all sessions visible to student (active view only)
                $allSessions = ChatSession::with(['counselor', 'latestMessage'])
                    ->activeForStudent($student->id_student)
                    ->orderBy('updated_at', 'desc')
                    ->get();

                // Get unread message counts
                foreach ($allSessions as $session) {
                    $session->unread_count = $session->getUnreadCountForStudent();
                }

            } else {
                return redirect()->back()->with('error', 'Invalid user role');
            }
            
            return view('public.konseling.index', compact('counselors', 'allSessions'));
        } catch (\Exception $e) {
            Log::error('Error in konseling index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    // ============================================
    // SHOW - Show specific session
    // ============================================
    public function show($id_session)
    {
        try {
            $session = ChatSession::with('counselor')->find($id_session);

            if (!$session) {
                return redirect()->route('public.konseling.index')->with('error', 'Session not found');
            }

            $user = Auth::user();

            // Check authorization
            if ($user->role === 'siswa') {
                $student = Student::where('user_id', $user->id)->first();
                if (!$student || $student->id_student !== $session->id_student) {
                    return redirect()->route('public.konseling.index')->with('error', 'Unauthorized access');
                }

                // Check if student has hidden this session
                $viewStatus = $session->getViewStatus('student', $student->id_student);
                if ($viewStatus === 'hidden') {
                    return redirect()->route('public.konseling.index')->with('error', 'Session not found');
                }

                // Get all sessions for sidebar
                $allSessions = ChatSession::with(['counselor', 'latestMessage'])
                    ->activeForStudent($student->id_student)
                    ->orderBy('updated_at', 'desc')
                    ->get();

                // Get unread message counts for sidebar
                foreach ($allSessions as $s) {
                    $s->unread_count = $s->getUnreadCountForStudent();
                }

            } else {
                return redirect()->route('public.konseling.index')->with('error', 'Invalid user role');
            }

            $counselors = Counselor::all();
            return view('public.konseling.index', compact('session', 'counselors', 'allSessions'));

        } catch (\Exception $e) {
            Log::error('Error showing session: ' . $e->getMessage());
            return redirect()->route('public.konseling.index')->with('error', 'Failed to load session');
        }
    }

    // ============================================
    // START SESSION - Show preview or existing ended session
    // ============================================
    public function startSession($id_counselor)
    {
        try {
            $user = Auth::user();

            if ($user->role !== 'siswa') {
                return redirect()->back()->with('error', 'Only students can start sessions');
            }

            $student = Student::where('user_id', $user->id)->first();
            if (!$student) {
                return redirect()->back()->with('error', 'Student profile not found');
            }

            // Check for ANY existing session with this counselor (active view only)
            $existingSession = ChatSession::where('id_student', $student->id_student)
                ->where('id_counselor', $id_counselor)
                ->activeForStudent($student->id_student) // Not archived or hidden
                ->first();

            // If there's an active session, redirect to it
            if ($existingSession && $existingSession->is_active) {
                Log::info("Student has active session, redirecting", [
                    'session_id' => $existingSession->id_session,
                    'counselor_id' => $id_counselor
                ]);
                return redirect()->route('public.konseling.show', $existingSession->id_session);
            }

            // If there's an ended session (not archived/hidden), show it
            if ($existingSession && !$existingSession->is_active) {
                Log::info("Student has ended session, showing it", [
                    'session_id' => $existingSession->id_session,
                    'counselor_id' => $id_counselor
                ]);
                return redirect()->route('public.konseling.show', $existingSession->id_session);
            }

            // No existing session - show preview mode
            $counselor = Counselor::find($id_counselor);
            if (!$counselor) {
                return redirect()->back()->with('error', 'Counselor not found');
            }

            Log::info("No active/ended session found, showing preview mode", [
                'student_id' => $student->id_student,
                'counselor_id' => $id_counselor
            ]);

            $allSessions = ChatSession::with(['counselor', 'latestMessage'])
                ->activeForStudent($student->id_student)
                ->orderBy('updated_at', 'desc')
                ->get();

            foreach ($allSessions as $s) {
                $s->unread_count = $s->getUnreadCountForStudent();
            }

            $counselors = Counselor::all();

            // Create preview session object
            $session = (object) [
                'id_session' => null,
                'id_student' => $student->id_student,
                'id_counselor' => $id_counselor,
                'counselor' => $counselor,
                'topic' => 'General Consultation',
                'is_active' => true,
                'preview_mode' => true,
                'welcome_message' => $counselor->auto_send_welcome && !empty($counselor->default_chat_message)
                    ? str_replace('{nama}', $student->user->name ?? 'Siswa', $counselor->default_chat_message)
                    : null,
            ];

            return view('public.konseling.index', compact('session', 'counselors', 'allSessions'));

        } catch (\Exception $e) {
            Log::error('Error starting session: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'counselor_id' => $id_counselor
            ]);
            return redirect()->back()->with('error', 'Failed to start session');
        }
    }

    // ============================================
    // CREATE NEW SESSION (CLEAN VERSION)
    // ============================================
    public function createNewSession(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'counselor_id' => 'required|integer|exists:counselor,id_counselor',
                'action' => 'required|in:keep,delete',
                'old_session_id' => 'required|integer|exists:chat_sessions,id_session'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid input data',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            if (!$user || $user->role !== 'siswa') {
                return response()->json([
                    'success' => false, 
                    'message' => 'Only students can create new sessions'
                ], 403);
            }

            $student = Student::where('user_id', $user->id)->first();
            if (!$student) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Student profile not found'
                ], 404);
            }

            $oldSession = ChatSession::find($request->old_session_id);
            if (!$oldSession || $oldSession->id_student !== $student->id_student) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Unauthorized access to session'
                ], 403);
            }

            // Verify the old session is ended
            if ($oldSession->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot create new session while current session is still active'
                ], 422);
            }

            $counselor = Counselor::find($request->counselor_id);
            if (!$counselor) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Counselor not found'
                ], 404);
            }

            // CLEAN LOGIC: Just update the view status
            try {
                if ($request->action === 'delete') {
                    Log::info("Student hiding session {$oldSession->id_session}");
                    $oldSession->hideFor('student', $student->id_student);
                    
                } elseif ($request->action === 'keep') {
                    Log::info("Student archiving session {$oldSession->id_session}");
                    $oldSession->archiveFor('student', $student->id_student);
                }

                Log::info("Session view updated successfully");

                // Redirect to preview mode for starting new session
                $redirectUrl = route('public.konseling.start', $counselor->id_counselor);

                Log::info("Redirecting to preview mode", [
                    'old_session_id' => $oldSession->id_session,
                    'counselor_id' => $counselor->id_counselor,
                    'action' => $request->action,
                    'redirect_url' => $redirectUrl
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $request->action === 'keep' 
                        ? 'Session archived successfully. You can now start a new session.' 
                        : 'Session deleted successfully. You can now start a new session.',
                    'redirect_url' => $redirectUrl
                ]);

            } catch (\Exception $e) {
                Log::error('Failed to update session view: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'session_id' => $request->old_session_id,
                    'action' => $request->action
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process request: ' . $e->getMessage(),
                    'error' => config('app.debug') ? $e->getTraceAsString() : null
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Create new session failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to process request. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ============================================
    // VIEW ARCHIVE
    // ============================================
    public function viewArchive()
{
    try {
        $user = Auth::user();
        if ($user->role !== 'siswa') {
            return redirect()->back()->with('error', 'Access denied');
        }

        $student = Student::where('user_id', $user->id)->first();
        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found');
        }

        // Get archived sessions with additional data
        $archivedSessions = DB::table('chat_sessions as cs')
            ->join('session_views as sv', 'cs.id_session', '=', 'sv.id_session')
            ->join('counselor as c', 'cs.id_counselor', '=', 'c.id_counselor')
            ->leftJoin(DB::raw('(SELECT id_session, COUNT(*) as total_messages, MAX(sent_at) as last_message_at 
                               FROM chat_messages GROUP BY id_session) as msg'), 'cs.id_session', '=', 'msg.id_session')
            ->where('sv.user_type', 'student')
            ->where('sv.user_id', $student->id_student)
            ->where('sv.view_status', 'archived')
            ->select(
                'cs.id_session',
                'cs.id_counselor',
                'cs.topic',
                'cs.created_at as session_started_at',
                'cs.ended_at as session_ended_at',
                'c.counselor_name',
                'c.photo as counselor_photo',
                'sv.archived_at',
                DB::raw('COALESCE(msg.total_messages, 0) as total_messages'),
                'msg.last_message_at'
            )
            ->orderBy('sv.archived_at', 'desc')
            ->get();

        Log::info("Retrieved {$archivedSessions->count()} archived sessions for student {$student->id_student}");

        return view('public.konseling.archive', compact('archivedSessions'));

    } catch (\Exception $e) {
        Log::error('Error viewing archive: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to load archive');
    }
}

    // ============================================
    // SHOW ARCHIVED SESSION
    // ============================================
    public function showArchive($sessionId)
{
    try {
        $user = Auth::user();
        if ($user->role !== 'siswa') {
            return redirect()->back()->with('error', 'Access denied');
        }

        $student = Student::where('user_id', $user->id)->first();
        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found');
        }

        $session = ChatSession::with(['counselor', 'messages'])->find($sessionId);
        
        if (!$session || $session->id_student !== $student->id_student) {
            return redirect()->back()->with('error', 'Session not found');
        }

        // Check if this session is actually archived by student
        $viewStatus = $session->getViewStatus('student', $student->id_student);
        if ($viewStatus !== 'archived') {
            return redirect()->route('public.konseling.index')
                ->with('error', 'This session is not in your archive');
        }

        // Get archive record with additional info
        $archiveRecord = DB::table('session_views')
            ->where('id_session', $sessionId)
            ->where('user_type', 'student')
            ->where('user_id', $student->id_student)
            ->first();

        // Add session start/end dates to archive record
        if ($archiveRecord) {
            $archiveRecord->session_started_at = $session->created_at;
            $archiveRecord->session_ended_at = $session->ended_at;
        }

        $messages = $session->messages;

        return view('public.konseling.archive-show', compact('session', 'messages', 'archiveRecord'));

    } catch (\Exception $e) {
        Log::error('Error showing archive: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        return redirect()->route('public.konseling.archive-list')
            ->with('error', 'Failed to load archived session');
    }
}


    // ============================================
    // DELETE SESSION (Hide from student's view)
    // ============================================
    public function deleteSession($id_session)
    {
        try {
            $user = Auth::user();
            if ($user->role !== 'siswa') {
                return response()->json(['error' => 'Only students can delete sessions'], 403);
            }

            $student = Student::where('user_id', $user->id)->first();
            if (!$student) {
                return response()->json(['error' => 'Student profile not found'], 404);
            }

            $session = ChatSession::find($id_session);
            if (!$session || $session->id_student !== $student->id_student) {
                return response()->json(['error' => 'Session not found'], 404);
            }

            // Check if session is active
            if ($session->is_active) {
                return response()->json([
                    'error' => 'Cannot delete active session. Please wait for counselor to end it.'
                ], 422);
            }

            Log::info("Student hiding session {$id_session}");

            // Hide the session from student's view
            $session->hideFor('student', $student->id_student);

            $message = 'Session deleted from your view';
            
            // Check if both users have hidden it
            if ($session->isHiddenByBoth()) {
                $message .= ' (Both parties have deleted this session)';
            } else {
                $message .= ' (Counselor can still access it)';
            }

            Log::info("Session {$id_session} hidden by student");
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'hidden_by_both' => $session->isHiddenByBoth()
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting session (student): ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Failed to delete session. Please try again.',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ============================================
    // STORE MESSAGE (Send message)
    // ============================================
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_session' => 'nullable|integer|exists:chat_sessions,id_session',
                'id_counselor' => 'required_if:id_session,null|integer',
                'message' => 'required|string|max:1000',
                'timezone' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Validation failed', 'errors' => $validator->errors()], 422);
            }

            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Not authenticated'], 401);
            }

            $userTimezone = $request->input('timezone', session('timezone', 'UTC'));
            session(['timezone' => $userTimezone]);

            $session = null;
            $isNewSession = false;

            if ($request->id_session) {
                $session = ChatSession::find($request->id_session);
                if (!$session) {
                    return response()->json(['error' => 'Session not found'], 404);
                }

                if ($user->role === 'siswa') {
                    $student = Student::where('user_id', $user->id)->first();
                    
                    // Check if student has hidden this session
                    $viewStatus = $session->getViewStatus('student', $student->id_student);
                    if ($viewStatus === 'hidden') {
                        return response()->json(['error' => 'Session no longer accessible'], 422);
                    }
                }
            } else {
                if ($user->role !== 'siswa') {
                    return response()->json(['error' => 'Only students can create new sessions'], 403);
                }

                $student = Student::where('user_id', $user->id)->first();
                if (!$student) {
                    return response()->json(['error' => 'Student profile not found'], 404);
                }

                $counselor = Counselor::find($request->id_counselor);
                if (!$counselor) {
                    return response()->json(['error' => 'Counselor not found'], 404);
                }

                // Check for existing active session
                $existingSession = ChatSession::where('id_student', $student->id_student)
                    ->where('id_counselor', $counselor->id_counselor)
                    ->where('is_active', 1)
                    ->activeForStudent($student->id_student)
                    ->first();

                if ($existingSession) {
                    $session = $existingSession;
                } else {
                    // Create new session
                    $session = ChatSession::create([
                        'id_student' => $student->id_student,
                        'id_counselor' => $counselor->id_counselor,
                        'topic' => 'General Consultation',
                        'is_active' => 1,
                    ]);
                    $isNewSession = true;
                }
            }

            $senderInfo = $this->getSenderInfo($user, $session);
            if (!$senderInfo) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }

            if ($user->role === 'siswa' && !$session->is_active) {
                return response()->json(['error' => 'Cannot send messages to ended sessions'], 422);
            }

            $messages = [];
            $nowUtc = Carbon::now('UTC');

            // Send welcome message if new session
            if ($isNewSession && $senderInfo['type'] === 'student') {
                $welcomeMessage = $this->sendWelcomeMessage($session, $nowUtc, $userTimezone);
                if ($welcomeMessage) {
                    $messages[] = $welcomeMessage;
                }
            }

            // Send user message
            $userMessage = ChatMessage::create([
                'id_session' => $session->id_session,
                'sender_type' => $senderInfo['type'],
                'id_sender' => $senderInfo['id'],
                'message' => $request->message,
                'status' => 'sent',
                'sent_at' => $nowUtc
            ]);

            $this->sendMessageNotification($session, $senderInfo, $userMessage);

            $displayTime = $nowUtc->setTimezone($userTimezone);
            $messages[] = [
                'id_message' => $userMessage->id_message,
                'id_session' => $userMessage->id_session,
                'sender_type' => $userMessage->sender_type,
                'id_sender' => $userMessage->id_sender,
                'message' => $userMessage->message,
                'status' => $userMessage->status,
                'sent_at' => $displayTime->format('H:i'),
                'date' => $displayTime->toDateString(),
                'sender_name' => $senderInfo['name'],
                'session_id' => $session->id_session,
                'is_first_message' => $isNewSession
            ];

            usort($messages, fn($a, $b) => $a['id_message'] <=> $b['id_message']);

            // Broadcast messages
            try {
                foreach ($messages as $msg) {
                    if (isset($msg['id_message'])) {
                        $messageModel = ChatMessage::find($msg['id_message']);
                        if ($messageModel) {
                            event(new MessageSent($messageModel));
                            Log::info('Broadcasting message', [
                                'message_id' => $messageModel->id_message,
                                'session_id' => $messageModel->id_session,
                                'sender_type' => $messageModel->sender_type
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Pusher broadcast failed: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'messages' => $messages,
                'session_id' => $session->id_session,
                'is_new_session' => $isNewSession
            ], 201);

        } catch (\Exception $e) {
            Log::error('Chat message store failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send message', 'message' => config('app.debug') ? $e->getMessage() : 'Server error'], 500);
        }
    }

    private function getSenderInfo($user, $session)
    {
        if ($user->role === 'siswa') {
            $student = Student::where('user_id', $user->id)->first();
            if (!$student || $student->id_student !== $session->id_student) {
                return null;
            }
            return ['type' => 'student', 'id' => $student->id_student, 'name' => $user->name ?? 'Siswa'];
        }
        return null;
    }

    private function sendWelcomeMessage($session, $nowUtc, $userTimezone)
    {
        $counselor = Counselor::find($session->id_counselor);
        if (!$counselor || !$counselor->auto_send_welcome || empty($counselor->default_chat_message)) {
            return null;
        }

        $studentName = $session->student->user->name ?? 'Siswa';
        $welcomeText = str_replace('{nama}', $studentName, $counselor->default_chat_message);
        $welcomeTime = $nowUtc->copy()->subSeconds(2);

        $welcomeMessage = ChatMessage::create([
            'id_session' => $session->id_session,
            'sender_type' => 'counselor',
            'id_sender' => $counselor->id_counselor,
            'message' => $welcomeText,
            'status' => 'sent',
            'sent_at' => $welcomeTime
        ]);

        $displayTime = $welcomeTime->setTimezone($userTimezone);

        return [
            'id_message' => $welcomeMessage->id_message,
            'id_session' => $welcomeMessage->id_session,
            'sender_type' => $welcomeMessage->sender_type,
            'id_sender' => $welcomeMessage->id_sender,
            'message' => $welcomeMessage->message,
            'status' => $welcomeMessage->status,
            'sent_at' => $displayTime->format('H:i'),
            'date' => $displayTime->toDateString(),
            'sender_name' => $counselor->counselor_name ?? 'Konselor',
            'is_welcome' => true
        ];
    }

    private function sendMessageNotification($session, $senderInfo, $message)
    {
        if ($senderInfo['type'] === 'student') {
            $counselor = Counselor::find($session->id_counselor);
            if ($counselor && $counselor->user_id) {
                NotificationService::send(
                    $counselor->user_id,
                    "Anda menerima pesan baru dari {$senderInfo['name']}.",
                    'chat_message',
                    [
                        'session_id' => $session->id_session,
                        'message_id' => $message->id_message,
                        'message_text' => substr($message->message, 0, 100) . (strlen($message->message) > 100 ? '...' : ''),
                        'url' => route('admin.konseling.show', $session->id_session),
                    ]
                );
            }
        }
    }

    public function fetchMessages($id_session)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Not authenticated'], 401);
            }

            $userTimezone = session('timezone', 'UTC');
            $session = ChatSession::with(['student.user', 'counselor'])->find($id_session);
            if (!$session) {
                return response()->json(['error' => 'Session not found'], 404);
            }

            $hasAccess = false;
            if ($user->role === 'siswa') {
                $student = Student::where('user_id', $user->id)->first();
                $hasAccess = $student && $student->id_student === $session->id_student;
            }

            if (!$hasAccess) {
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
            Log::error('Error fetching messages: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch messages'], 500);
        }
    }

    public function markAsRead($id_session)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'siswa') {
                return response()->json(['error' => 'Only students can mark messages in this view'], 403);
            }

            $session = ChatSession::find($id_session);
            if (!$session) {
                return response()->json(['error' => 'Session not found'], 404);
            }

            $student = Student::where('user_id', $user->id)->first();
            if (!$student || $student->id_student !== $session->id_student) {
                return response()->json(['error' => 'Unauthorized access to this session'], 403);
            }

            // Get unread counselor messages
            $unreadMessages = ChatMessage::where('id_session', $id_session)
                ->where('sender_type', 'counselor')
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
                ->where('sender_type', 'counselor')
                ->where('status', 'sent')
                ->update(['status' => 'read']);

            Log::info('Messages marked as read', [
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
            Log::error('Error marking messages as read (student): ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to mark messages as read',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}