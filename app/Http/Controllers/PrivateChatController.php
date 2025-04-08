<?php

namespace App\Http\Controllers;

use App\Models\PrivateChat;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class PrivateChatController extends Controller
{
    public function createChat($name)
    {
        try {
            $target = User::where('name', $name)->firstOrFail();
            $me = auth()->id();
            $them = $target->id;

            $chat = PrivateChat::firstOrCreate([
                'user_one_id' => min($me, $them),
                'user_two_id' => max($me, $them)
            ]);

            return Inertia::render('ChatRooms/PrivateChat', [
                'chat' => $chat->load(['userOne', 'userTwo']),
                'otherUser' => $target
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to start chat');
        }
    }

    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'chat_id' => 'required|integer'
        ]);

        // Encrypt message via Python microservice
        $encryptionResponse = Http::withoutVerifying()->post('https://127.0.0.1:5000/encrypt', [
            'message' => $validated['message']
        ]);

        if ($encryptionResponse->failed()) {
            return response()->json(['error' => 'Encryption failed'], 500);
        }

        $encryptedData = $encryptionResponse->json();
        $encryptedMessage = $encryptedData['encrypted_message'];
        $iv = $encryptedData['iv'];
        $kyberCiphertext = $encryptedData['kyber_ciphertext'];

        $message = new PrivateMessage();
        $message->chat_id = $validated['chat_id'];
        $message->sender_id = auth()->id();
        $message->encrypted_message = $encryptedMessage;
        $message->iv = $iv;
        $message->kyber_ciphertext = $kyberCiphertext;
        $message->save();

        broadcast(new \App\Events\PrivateMessageSent(
            $message->id,
            $message->chat_id,
            $encryptedMessage,
            $validated['message'],
            auth()->user()->name
        ))->toOthers();

        return response()->json($message);
    }

    public function getMessages($chatId)
    {
        $messages = PrivateMessage::with('sender') // Eager load sender relationship
        ->where('chat_id', $chatId)
        ->orderBy('created_at', 'asc')
        ->get();

        PrivateMessage::where('chat_id', $chatId)
        ->whereNull('read_at')
        ->update(['read_at' => now()]);

        $decryptedMessages = $messages->map(function ($msg) {
            $response = Http::withoutVerifying()->post('https://127.0.0.1:5000/decrypt', [
                'kyber_ciphertext' => $msg->kyber_ciphertext,
                'encrypted_message' => $msg->encrypted_message,
                'iv' => $msg->iv,
            ]);

            $decryptedContent = $response->successful()
                ? $response->json()['decrypted_message']
                : 'Decryption failed';

                return [
                    'id' => $msg->id,
                    'chat_id' => $msg->chat_id,
                    'sender_id' => $msg->sender_id,
                    'sender_name' => $msg->sender->name, // Add sender name
                    'content' => $decryptedContent,
                    'created_at' => $msg->created_at
                ];
        });

        return response()->json($decryptedMessages);
    }

    public function listChats()
{
    $user = auth()->user();
    $chats = PrivateChat::with(['userOne', 'userTwo', 'messages' => function($query) {
        $query->latest()->limit(1);
    }])
    ->where(function($query) use ($user) {
        $query->where('user_one_id', $user->id)
              ->orWhere('user_two_id', $user->id);
    })
    ->get()
    ->map(function($chat) use ($user) {
        $otherUser = $chat->user_one_id === $user->id ? $chat->userTwo : $chat->userOne;

return [
    'id' => $chat->id,
    'other_user' => [
        'id' => $otherUser->id,
        'name' => $otherUser->name
    ],
    'last_message' => $chat->messages->first() ? [
        'content' => $chat->messages->first()->content,
        'created_at' => $chat->messages->first()->created_at
    ] : null,
    'updated_at' => $chat->updated_at ?? $chat->created_at,
    'unread_count' => $chat->messages()
        ->where('sender_id', '!=', $user->id)
        ->whereNull('read_at')
        ->count()
];

    });

    return response()->json($chats);

}


}
