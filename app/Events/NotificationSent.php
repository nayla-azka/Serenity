<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $recipientType;
    public $recipientId;
    public $message;
    public $type;

    public function __construct($recipientType, $recipientId = null, $message, $type)
    {
        $this->recipientType = $recipientType;
        $this->recipientId   = $recipientId;
        $this->message       = $message;
        $this->type          = $type;
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'type'    => $this->type,
            'time'    => now()->toDateTimeString(),
            'recipient_type' => $this->recipientType,
            'recipient_id' => $this->recipientId
        ];
    }

    public function broadcastOn()
    {
        $channels = [];
        
        switch ($this->recipientType) {
            case 'siswa':
                // Private channel for specific student
                if ($this->recipientId) {
                    $channels[] = new PrivateChannel('user.' . $this->recipientId);
                }
                break;
                
            case 'konselor':
                // Private channel for specific konselor
                if ($this->recipientId) {
                    $channels[] = new PrivateChannel('konselor.' . $this->recipientId);
                    // Also send to their user channel for consistency
                    $channels[] = new PrivateChannel('user.' . $this->recipientId);
                }
                break;
                
            case 'admin':
                // Public channel for all admins (real-time broadcast)
                $channels[] = new Channel('admin');
                break;
        }
        
        return $channels;
    }

    public function broadcastAs()
    {
        return 'notification.sent';
    }
}