<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessage;
use App\Models\ChatRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;


class ChatController extends Controller
{

    public function listRooms(Request $request)
    {
        $rooms = ChatRoom::all()->map(function ($room) {
            return [
                'id' => $room->id,
                'name' => $room->name,
                'description' => $room->description,
                'max_members' => $room->max_members,
                'is_ephemeral' => $room->is_ephemeral,
                'self_destruct_hours' => $room->self_destruct_hours,
                'created_at' => $room->created_at->diffForHumans()
            ];
        });

        return response()->json($rooms);
    }

    public function joinRoom(Request $request, $roomId)
    {
        $room = ChatRoom::findOrFail($roomId);

        // Count active users in the room
        $activeUsers = cache()->get("chat_room_users_{$roomId}", []);
        if (count($activeUsers) >= $room->max_members) {
            return redirect()->route('listrooms')->with('error', 'Room is full.');
        }

        // Store user in active users cache
        $userId = auth()->id();
        $activeUsers[$userId] = ['id' => $userId, 'name' => auth()->user()->anonymous_alias];
        cache()->put("chat_room_users_{$roomId}", $activeUsers, now()->addMinutes(10));

        return Inertia::render('ChatRooms/Room', [
            'roomId' => $roomId,
            'room' => $room,
            'members' => array_values($activeUsers),
        ]);
    }


    public function sendMessage(Request $request, $roomId)
    {
        $validated = $request->validate([
            'encrypted_message' => 'required|string',
            'iv' => 'required|string'
        ]);

        $room = ChatRoom::findOrFail($roomId);

        // Store in database
        $message = $room->messages()->create([
            'user_id' => $request->user()->id,
            'encrypted_message' => $validated['encrypted_message'],
            'iv' => $validated['iv']
        ]);

        // Broadcast the message
        event(new NewChatMessage(
            $roomId,
            $validated['encrypted_message'],
            $request->user()->anonymous_alias
        ));

        return response()->json(['status' => 'Message dispatched to void']);
    }




    private function initiateQuantumKeyExchange($user)
    {
        // Integrate with Python microservice for Kyber
        // This would make an API call to your Python service
        return 'KYBER_SHARED_SECRET'; // Placeholder
    }
}
