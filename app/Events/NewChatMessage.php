<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomId;
    public $encryptedMessage;
    public $senderId;

    /**
    * Create a new event instance.
    */

    public function __construct( $roomId, $encryptedMessage, $senderId ) {
        $this->roomId = $roomId;
        $this->encryptedMessage = $encryptedMessage;
        $this->senderId = $senderId;
    }

    public function broadcastOn() {
        return new Channel( 'chat.'.$this->roomId );
    }

    public function broadcastWith() {
        return [
            'message' => $this->encryptedMessage,
            'sender' => $this->senderId,
            'timestamp' => now()->toISOString()
        ];
    }
}
