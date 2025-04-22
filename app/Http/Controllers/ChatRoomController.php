<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessage;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage; 

class ChatRoomController extends Controller
{
      /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('ChatRooms/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:chat_rooms',
            'description' => 'nullable|string',
            'max_members' => 'nullable|integer|min:1',
            'is_ephemeral' => 'required|boolean',
            'self_destruct_hours' => 'required_if:is_ephemeral,true|nullable|integer|min:1'
        ]);

        ChatRoom::create($validated);

        return redirect()->route('hell.chatrooms')->with('message', 'Chamber forged in the void!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChatRoom $chatroom)
    {
        return Inertia::render('ChatRooms/Edit', [
            'chatroom' => [
                'id' => $chatroom->id,
                'name' => $chatroom->name,
                'description' => $chatroom->description,
                'max_members' => $chatroom->max_members,
                'is_ephemeral' => $chatroom->is_ephemeral,
                'self_destruct_hours' => $chatroom->self_destruct_hours,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChatRoom $chatroom)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:chat_rooms,name,'.$chatroom->id,
            'description' => 'nullable|string',
            'max_members' => 'nullable|integer|min:1',
            'is_ephemeral' => 'required|boolean',
            'self_destruct_hours' => 'required_if:is_ephemeral,true|nullable|integer|min:1'
        ]);

        $chatroom->update($validated);

        return redirect()->route('hell.chatrooms')->with('message', 'Chamber twisted successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChatRoom $chatroom)
    {
        $chatroom->delete();
        return redirect()->route('hell.chatrooms')
            ->with('message', 'Chamber eradicated from existence!');
    }
        /**
     * Return a list of all chat rooms.
     */
    public function listRooms(Request $request){
        $rooms = ChatRoom::all()->map(function ($room) {
            return [
                'id'                => $room->id,
                'name'              => $room->name,
                'description'       => $room->description,
                'max_members'       => $room->max_members,
                'is_ephemeral'      => $room->is_ephemeral,
                'self_destruct_hours'=> $room->self_destruct_hours,
                'created_at'        => $room->created_at->diffForHumans()
            ];
        });

        return response()->json($rooms);
    }

    /**
     * Handle user joining a chat room.
     * Enforces the room capacity and stores the active user in cache.
     */
    public function joinRoom(Request $request, $roomId)
    {
        $room = ChatRoom::findOrFail($roomId);
        $activeUsers = cache()->get("chat_room_users_{$roomId}", []);
        $user = auth()->user();
    
        if (isset($activeUsers[$user->id])) {
            return Inertia::render('ChatRooms/Room', [
                'roomId'  => $roomId,
                'room'    => $room,
                'members' => array_values($activeUsers),
            ]);
        }
    
        if (count($activeUsers) >= $room->max_members && !$user->is_god_user) {
            return redirect()->route('listrooms')->with('error', 'Room is full.');
        }
    
        $activeUsers[$user->id] = ['id' => $user->id, 'name' => $user->name];
        cache()->put("chat_room_users_{$roomId}", $activeUsers, now()->addMinutes(10));
    
        return Inertia::render('ChatRooms/Room', [
            'roomId'  => $roomId,
            'room'    => $room,
            'members' => array_values($activeUsers),
        ]);
    }


    public function leaveRoom(Request $request, $roomId)
    {
        $activeUsers = cache()->get("chat_room_users_{$roomId}", []);
        $userId = auth()->id();
        if (isset($activeUsers[$userId])) {
            unset($activeUsers[$userId]);
            cache()->put("chat_room_users_{$roomId}", $activeUsers, now()->addMinutes(10));
        }
        return response()->json(['status' => 'Left room']);
    }
}
