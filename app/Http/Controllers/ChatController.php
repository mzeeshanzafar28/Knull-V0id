<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessage;
use App\Models\ChatRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

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
                'destruction_time' => $room->destruction_time,
                'created_at' => $room->created_at->diffForHumans()
            ];
        });

        return response()->json($rooms);
    }

    public function joinRoom(Request $request, $roomId)
    {
        $room = ChatRoom::findOrFail($roomId);
        
        // Implement quantum-safe key exchange here
        $sharedSecret = $this->initiateQuantumKeyExchange($request->user());
        
        return response()->json([
            'room' => $room,
            'ephemeral_key' => Crypt::encrypt($sharedSecret)
        ]);
    }

    public function sendMessage(Request $request, $roomId)
    {
        $validated = $request->validate([
            'encrypted_message' => 'required|string',
            'iv' => 'required|string'
        ]);

        // Store encrypted message in DB or ephemeral storage
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