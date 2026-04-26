<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrivateMessageSent implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageId;
    public $chatId;
    public $senderName;
    public $mediaPath;
    public $mediaType;
    public $timestamp;
    public $clientId;

    public function __construct( $messageId, $chatId, $senderName, $mediaPath, $mediaType, $timestamp = null, $clientId = null ) {
        $this->messageId = $messageId;
        $this->chatId = $chatId;
        $this->senderName = $senderName;
        $this->mediaPath = $mediaPath;
        $this->mediaType = $mediaType;
        $this->timestamp = $timestamp ?? now()->toISOString();
        $this->clientId = $clientId;
    }

    public function broadcastOn() {
        return new PrivateChannel( 'private-chat.' . $this->chatId );
    }

    public function broadcastWith() {
        return [
            'id' => $this->messageId,
            'chat_id' => $this->chatId,
            'sender_name' => $this->senderName,
            'created_at' => $this->timestamp,
            'media_path' => $this->mediaPath,
            'media_type' => $this->mediaType,
            'client_id' => $this->clientId
        ];
    }
}
