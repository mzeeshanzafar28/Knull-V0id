<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomId;
    public $encryptedMessage;
    public $plainMessage;
    public $sender;

    public function __construct( $roomId, $encryptedMessage, $sender ) {
        $this->roomId = $roomId;
        $this->encryptedMessage = $encryptedMessage;
        $this->sender = $sender;
    }

    public function broadcastOn() {
        return new PrivateChannel( 'chat.'.$this->roomId );
    }

    public function broadcastWith() {
        return [
            'message'   => $this->plainMessage,
            'sender'    => $this->sender,
            'timestamp' => now()->toISOString()
        ];
    }

    public function broadcastAs() {
        return 'NewChatMessage';
    }
}
