<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;

class ChatController1 extends Controller
{
    public function index()
    {
        // load all sessions with counselor info
        $sessions = ChatSession::with('counselor')->get();
        return view('public.konseling.index', compact('sessions'));
    }

    public function show($id_session)
    {
        // also eager load counselor
        $session = ChatSession::with(['messages', 'counselor'])->findOrFail($id_session);
        $allSessions = ChatSession::with('counselor')->get();

        return view('public.konseling.show', [
            'session' => $session,
            'messages' => $session->messages,
            'allSessions' => $allSessions
        ]);
    }



    // Kirim pesan baru
    public function store(Request $request)
    {
        $request->validate([
            'id_session' => 'required|exists:chat_sessions,id_session',
            'message' => 'required|string',
        ]);

        ChatMessage::create([
            'id_session' => $request->id_session,
            'sender_type' => Auth::user()->peran ?? 'student', // peran dari user login
            'id_sender' => Auth::id(),
            'message' => $request->message,
            'status' => 'sent',
        ]);

        return redirect()->back();
    }
}
