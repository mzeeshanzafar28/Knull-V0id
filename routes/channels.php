<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\ChatRoom;
use App\Models\PrivateChat;


// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
//     // return true;

// });

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});


Broadcast::routes(['middleware' => ['web', 'auth']]);


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

Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    return PrivateChat::where('id', $chatId)
        ->where(function($q) use ($user) {
            $q->where('user_one_id', $user->id)
              ->orWhere('user_two_id', $user->id);
        })->exists();
});
