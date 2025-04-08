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
            'message' => [
                'id' => $this->messageId,
                'chat_id' => $this->chatId,
                'sender_name' => $this->senderName,
                'content' => $this->decryptedMessage,
                'encrypted_message' => $this->encryptedMessage,
                'created_at' => now()->toDateTimeString()
            ]
        ];
    }
}
