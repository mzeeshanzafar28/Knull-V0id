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
    public $mediaPath;
    public $mediaType;

    public function __construct( $roomId, $encryptedMessage, $plainMessage, $sender, $mediaPath = null, $mediaType = null ) {
        $this->roomId = $roomId;
        $this->encryptedMessage = $encryptedMessage;
        $this->plainMessage = $plainMessage;
        $this->sender = $sender;
        $this->mediaPath = $mediaPath;
        $this->mediaType = $mediaType;
    }

    public function broadcastOn() {
        return new PrivateChannel( 'chat.'.$this->roomId );
    }

    public function broadcastWith() {
        return [
            'message'   => $this->plainMessage,
            'sender'    => $this->sender,
            'timestamp' => now()->toISOString(),
            'media_path' => $this->mediaPath,
            'media_type' => $this->mediaType
        ];
    }

    public function broadcastAs() {
        return 'NewChatMessage';
    }
}
