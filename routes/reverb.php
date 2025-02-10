<?php
use App\Events\NewChatMessage;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{roomId}', function ($user, $roomId) {
    return [
        'id' => $user->id,
        'name' => $user->anonymous_alias
    ];
});
