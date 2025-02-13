<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessage;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class ChatController extends Controller
{
    /**
     * Return a list of all chat rooms.
     */
    public function listRooms(Request $request)
    {
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

        // Count active users in the room using cache
        $activeUsers = cache()->get("chat_room_users_{$roomId}", []);
        if (count($activeUsers) >= $room->max_members) {
            return redirect()->route('listrooms')->with('error', 'Room is full.');
        }

        // Add current user to active users
        $userId = auth()->id();
        $activeUsers[$userId] = ['id' => $userId, 'name' => auth()->user()->anonymous_alias];
        cache()->put("chat_room_users_{$roomId}", $activeUsers, now()->addMinutes(10));

        return Inertia::render('ChatRooms/Room', [
            'roomId'  => $roomId,
            'room'    => $room,
            'members' => array_values($activeUsers),
        ]);
    }

    /**
     * Send a message in a chat room.
     * Encrypts the message via a Python microservice, stores it, and broadcasts it.
     */
    public function sendMessage(Request $request, $roomId)
    {
        $validated = $request->validate([
            'message' => 'required|string'
        ]);

        $room = ChatRoom::findOrFail($roomId);

        // Encrypt message via Python microservice
        $encryptionResponse = Http::post('http://127.0.0.1:5000/encrypt', [
            'message' => $validated['message']
        ]);

        if ($encryptionResponse->failed()) {
            return response()->json(['error' => 'Encryption failed'], 500);
        }

        $encryptedData = $encryptionResponse->json();
        $encryptedMessage = $encryptedData['encrypted_message'];
        $iv = $encryptedData['iv'];

        // Store encrypted message in the database
        $message = ChatMessage::create([
            'chat_room_id'     => $roomId,
            'user_id'          => auth()->id(),
            'encrypted_message'=> $encryptedMessage,
            'iv'               => $iv,
            'sender'           => auth()->user()->name,
        ]);

        // Broadcast the encrypted message to the room

        event(new NewChatMessage($roomId, $encryptedMessage,$validated['message']  , auth()->user()->name));
        // NewChatMessage::dispatch($roomId, $encryptedMessage, auth()->user()->name);


        return response()->json(['status' => 'Message sent', 'sender' => auth()->user()->name]);
    }

    /**
     * Fetch all messages for a specific chat room.
     */
    public function fetchMessages(Request $request, $roomId)
    {
        $messages = ChatMessage::where('chat_room_id', $roomId)
                    ->orderBy('created_at', 'asc')
                    ->get();

        $decryptedMessages = $messages->map(function ($msg) {
            // Call the Python decryption service for each message
            $response = Http::post('http://127.0.0.1:5000/decrypt', [
                'encrypted_message' => $msg->encrypted_message,
                'iv' => $msg->iv,
            ]);

            // If the decryption is successful, use the decrypted message; otherwise set an error text
            $decryptedContent = $response->successful()
                ? $response->json()['decrypted_message']
                : 'Decryption failed';

            return array_merge($msg->toArray(), ['content' => $decryptedContent]);
        });

        return response()->json($decryptedMessages);
    }


    /**
     * Fetch active members for a specific chat room.
     */
    public function fetchMembers(Request $request, $roomId)
    {
        $activeUsers = cache()->get("chat_room_users_{$roomId}", []);
        return response()->json(array_values($activeUsers));
    }
}
