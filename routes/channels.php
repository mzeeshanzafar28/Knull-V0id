<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\ChatRoom;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    // return (int) $user->id === (int) $id;
    return true;

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
