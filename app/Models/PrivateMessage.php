<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivateMessage extends Model {
    protected $table = 'private_messages';
    public $timestamps = false;

    protected $fillable = [
        'chat_id',
        'sender_id',
        'encrypted_message',
        'iv',
        'kyber_ciphertext',
        'media_path',
        'media_type'
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function chat(): BelongsTo {
        return $this->belongsTo( PrivateChat::class, 'chat_id' );
    }

    public function sender(): BelongsTo {
        return $this->belongsTo( User::class, 'sender_id' );
    }

    protected static function booted() {
        static::deleting(function ($message) {
            if ($message->media_path) {
                Storage::disk('public')->delete($message->media_path);
            }
        });
    }

}
