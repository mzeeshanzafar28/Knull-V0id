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
        try {
            $validated = $request->validate([
                'message' => 'nullable|string',
                'chat_id' => 'required|integer',
                'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,webm|max:25600'
            ]);
    
            $mediaPath = null;
            $mediaType = null;
            $messageContent = $validated['message'] ?? '';
    
            if ($request->hasFile('media')) {
                $file = $request->file('media');
                $mediaPath = $file->store('private_media', 'public');
                $mediaType = $file->getMimeType();
            }
    
            $encryptedMessage = '';
            $iv = '';
            $kyber_ciphertext = '';
    
            if (!empty($messageContent)) {
                $encryptionResponse = Http::withoutVerifying()->post('https://127.0.0.1:5000/encrypt', [
                    'message' => $messageContent
                ]);
    
                if ($encryptionResponse->failed()) {
                    throw new \Exception('Message encryption failed');
                }
    
                $encryptedData = $encryptionResponse->json();
                $encryptedMessage = $encryptedData['encrypted_message'];
                $iv = $encryptedData['iv'];
                $kyber_ciphertext = $encryptedData['kyber_ciphertext'];
            }
    
            $message = new PrivateMessage();
            $message->chat_id = $validated['chat_id'];
            $message->sender_id = auth()->id();
            $message->encrypted_message = $encryptedMessage;
            $message->iv = $iv;
            $message->kyber_ciphertext = $kyber_ciphertext;
            $message->media_path = $mediaPath;
            $message->media_type = $mediaType;
            $message->save();
    
            broadcast(new \App\Events\PrivateMessageSent(
                $message->id,
                $message->chat_id,
                $encryptedMessage,
                $messageContent, // Use the actual content that was encrypted
                auth()->user()->name,
                $mediaPath,
                $mediaType
            ))->toOthers();
    
            return response()->json($message);
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Message sending failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMessages($chatId)
    {
        $messages = PrivateMessage::with('sender')
            ->where('chat_id', $chatId)
            ->orderBy('created_at', 'asc')
            ->get();
    
        $decryptedMessages = $messages->map(function ($msg) {
            // Handle media-only messages
            if (empty($msg->encrypted_message) && !empty($msg->media_path)) {
                return [
                    'id' => $msg->id,
                    'sender_name' => $msg->sender->name,
                    'content' => '', // No text content
                    'media_path' => $msg->media_path,
                    'media_type' => $msg->media_type,
                    'created_at' => $msg->created_at
                ];
            }
    
            // Only attempt decryption if encrypted content exists
            $decryptedContent = 'Media shared'; // Default for media-only
            if (!empty($msg->encrypted_message)) {
                $response = Http::withoutVerifying()->post('https://127.0.0.1:5000/decrypt', [
                    'kyber_ciphertext' => $msg->kyber_ciphertext,
                    'encrypted_message' => $msg->encrypted_message,
                    'iv' => $msg->iv,
                ]);
                
                $decryptedContent = $response->successful() 
                    ? $response->json()['decrypted_message']
                    : 'Decryption failed';
            }
    
            return [
                'id' => $msg->id,
                'sender_name' => $msg->sender->name,
                'content' => $decryptedContent,
                'media_path' => $msg->media_path,
                'media_type' => $msg->media_type,
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
