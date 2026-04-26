<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\ChatRoom;

class ChatMessage extends Model {
    protected $fillable = [
        'chat_room_id',
        'user_id',
        'encrypted_message',
        'kyber_ciphertext',
        'iv',
        'tag',
        'hkdf_salt',
        'kem_private_key',
        'sender',
        'media_path',
        'media_type'

    ];

    public function room() {
        return $this->belongsTo( ChatRoom::class, 'chat_room_id' );
    }

    public function user() {
        return $this->belongsTo( User::class );
    }

    protected static function booted()
    {
        static::deleting(function ($message) {
            // Delete associated media when message is deleted
            if ($message->media_path) {
                Storage::disk('public')->delete($message->media_path);
            }
        });
    }
    
}
