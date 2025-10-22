<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MessageRead implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sessionId;
    public $messageIds;

    public function __construct($sessionId, array $messageIds)
    {
        $this->sessionId = $sessionId;
        $this->messageIds = $messageIds;
       
        Log::info('✅ MessageRead event constructed', [
            'session_id' => $sessionId,
            'message_ids' => $messageIds,
            'count' => count($messageIds)
        ]);
    }

    /**
     * FIXED: Use correct channel format matching frontend
     */
    public function broadcastOn()
    {
        $channelName = 'chat.' . $this->sessionId;
        
        Log::info('📡 Broadcasting on channel', [
            'channel' => $channelName,
            'full_channel' => 'private-' . $channelName
        ]);
        
        return new PrivateChannel($channelName);
    }

    /**
     * Event name that frontend listens to
     */
    public function broadcastAs()
    {
        return 'messages.read';
    }

    /**
     * Data sent to frontend
     */
    public function broadcastWith()
    {
        $payload = [
            'session_id' => $this->sessionId,
            'message_ids' => $this->messageIds,
            'status' => 'read',
            'timestamp' => now()->toIso8601String(),
            'count' => count($this->messageIds)
        ];
        
        Log::info('📤 Broadcasting payload', $payload);
       
        return $payload;
    }
}