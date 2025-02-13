<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model {
    protected $fillable = [
        'chat_room_id',
        'user_id',
        'encrypted_message',
        'iv',
        'sender',
    ];

    public function room() {
        return $this->belongsTo( ChatRoom::class, 'chat_room_id' );
    }

    public function user() {
        return $this->belongsTo( User::class );
    }
}
