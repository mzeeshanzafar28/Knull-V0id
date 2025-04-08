<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrivateChat extends Model {
    // If your migration name/table name matches, you can omit this:
    protected $table = 'private_chats';

    // We only have a created_at column, no updated_at
    public $timestamps = false;

    // Which fields we can mass‑assign
    protected $fillable = [
        'user_one_id',
        'user_two_id',
    ];

    /**
    * The first participant in the chat.
    */

    public function userOne(): BelongsTo {
        return $this->belongsTo( User::class, 'user_one_id' );
    }

    /**
    * The second participant in the chat.
    */

    public function userTwo(): BelongsTo {
        return $this->belongsTo( User::class, 'user_two_id' );
    }

    /**
    * All messages that belong to this private chat.
    */

    public function messages(): HasMany {
        return $this->hasMany( PrivateMessage::class, 'chat_id' );
    }
}
