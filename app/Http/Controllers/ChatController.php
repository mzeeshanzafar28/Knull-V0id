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
                'self_destruct_hours' => $room->self_destruct_hours,
                'created_at' => $room->created_at->diffForHumans()
            ];
        });

        return response()->json($rooms);
    }

    public function joinRoom(Request $request, $roomId)
    {
        $request->validate([
            'timeHash' => 'required|string|size:64' 
        ]);

        $now = now();
        $currentTime = $now->format('H-i');
        $previousTime = $now->subMinute()->format('H-i');

        $currentHash = hash('sha256', $currentTime);
        $previousHash = hash('sha256', $previousTime);

        if ($request->timeHash !== $currentHash && $request->timeHash !== $previousHash) {
            return response()->json([
                'error' => 'Invalid Request. Access denied.'
            ], 403);
        }

        $room = ChatRoom::findOrFail($roomId);

        return Inertia::render('ChatRooms/Room', [
            'roomId' => $roomId,
            'room' => $room
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