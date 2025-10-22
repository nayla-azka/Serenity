<?php

namespace App\Events;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcastNow // Changed from ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $sessionId;

    public function __construct(ChatMessage $message)
    {
        try {
            Log::info('MessageSent Event Created', [
                'message_id' => $message->id_message,
                'session_id' => $message->id_session,
                'sender_type' => $message->sender_type
            ]);

            $session = ChatSession::with(['student.user', 'counselor'])->find($message->id_session);
            
            if (!$session) {
                Log::error('Session not found', ['session_id' => $message->id_session]);
                throw new \Exception('Session not found');
            }

            $this->sessionId = $message->id_session;

            $senderName = $message->sender_type === 'student'
                ? ($session->student->user->name ?? 'Student')
                : ($session->counselor->counselor_name ?? 'Counselor');

            $this->message = [
                'id_message' => $message->id_message,
                'id_session' => $message->id_session,
                'sender_type' => $message->sender_type,
                'id_sender' => $message->id_sender,
                'message' => $message->message,
                'status' => $message->status,
                 'sent_at' => $message->sent_at->toIso8601String(),
                'date' => $message->sent_at->toDateString(),
                'sender_name' => $senderName
            ];

            Log::info('Broadcasting to channel', [
                'channel' => 'private-chat.' . $this->sessionId,
                'event' => 'message.sent'
            ]);
            
        } catch (\Exception $e) {
            Log::error('MessageSent Event Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function broadcastOn()
    {
        // Return PrivateChannel - Pusher will add 'private-' prefix automatically
        return new PrivateChannel('chat.' . $this->sessionId);
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }

    public function broadcastWith()
    {
        return $this->message;
    }
}