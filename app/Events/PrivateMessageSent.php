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
    public $encryptedMessage;
    public $decryptedMessage;
    public $senderName;

    public function __construct( $messageId, $chatId, $encryptedMessage, $decryptedMessage, $senderName ) {
        $this->messageId = $messageId;
        $this->chatId = $chatId;
        $this->encryptedMessage = $encryptedMessage;
        $this->decryptedMessage = $decryptedMessage;
        $this->senderName = $senderName;
    }

    public function broadcastOn() {
        return new PrivateChannel( 'chat.' . $this->chatId );
    }

    public function broadcastWith() {
        return [
            'id' => $this->messageId,
            'chat_id' => $this->chatId,
            'content' => $this->decryptedMessage,
            'sender_name' => $this->senderName,
            'created_at' => now()->toISOString()
        ];
    }
}
