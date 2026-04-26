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
    public $messageId;
    public $sender;
    public $mediaPath;
    public $mediaType;
    public $timestamp;
    public $clientId;

    public function __construct( $messageId, $roomId, $sender, $mediaPath = null, $mediaType = null, $timestamp = null, $clientId = null ) {
        $this->messageId = $messageId;
        $this->roomId = $roomId;
        $this->sender = $sender;
        $this->mediaPath = $mediaPath;
        $this->mediaType = $mediaType;
        $this->timestamp = $timestamp ?? now()->toISOString();
        $this->clientId = $clientId;
    }

    public function broadcastOn() {
        return new PrivateChannel( 'chat.'.$this->roomId );
    }

    public function broadcastWith() {
        return [
            'message_id' => $this->messageId,
            'sender'     => $this->sender,
            'timestamp'  => $this->timestamp,
            'media_path' => $this->mediaPath,
            'media_type' => $this->mediaType,
            'client_id'  => $this->clientId
        ];
    }

    public function broadcastAs() {
        return 'NewChatMessage';
    }
}
