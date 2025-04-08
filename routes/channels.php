<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\ChatRoom;
use App\Models\PrivateChat;


Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
    // return true;

});


Broadcast::channel('chat.{roomId}', function ($user, $roomId) {
    if (!ChatRoom::where('id', $roomId)->exists()) {
        return response()->json(['error' => 'Room not found'], 403);
    }

    return [
        'id' => $user->id,
        'name' => $user->name,
        'room_id' => $roomId
    ];
});

Broadcast::channel('private-chat.{chatId}', function ($user, $chatId) {
    return PrivateChat::where('id', $chatId)
        ->where(function($query) use ($user) {
            $query->where('user_one_id', $user->id)
                  ->orWhere('user_two_id', $user->id);
        })
        ->exists();
});
