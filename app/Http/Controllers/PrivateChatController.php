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
    /**
     * The encryption API key shared with the Python microservice.
     */
    private function encryptionApiKey(): string
    {
        return config('services.encryption.api_key', 'knull-void-encryption-key-change-in-production');
    }

    /**
     * The encryption service base URL.
     */
    private function encryptionUrl(): string
    {
        return config('services.encryption.url', 'https://127.0.0.1:5000');
    }

    /**
     * Verify the current user is a participant in the given private chat.
     */
    private function authorizeChat(PrivateChat $chat): bool
    {
        $userId = auth()->id();
        return $chat->user_one_id === $userId || $chat->user_two_id === $userId;
    }

    public function createChat($name)
    {
        try {
            $target = User::where('name', $name)->firstOrFail();
            $me = auth()->id();
            $them = $target->id;

            if ($me === $them) {
                return redirect()->back()->with('error', 'Cannot chat with yourself');
            }

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
                'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:25600'
            ]);

            // Authorization: verify user is a participant
            $chat = PrivateChat::findOrFail($validated['chat_id']);
            if (!$this->authorizeChat($chat)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
    
            $mediaPath = null;
            $mediaType = null;
            $messageContent = $validated['message'] ?? '';
    
            if ($request->hasFile('media')) {
                $file = $request->file('media');
                $mediaPath = $file->store('private_media', 'public');
                $mediaType = $file->getMimeType();
            }
    
            $encryptedMessage = null;
            $iv = null;
            $kyber_ciphertext = null;
            $tag = null;
            $hkdf_salt = null;
            $kem_private_key = null;
    
            if (!empty($messageContent)) {
                $encryptionResponse = Http::withoutVerifying()
                    ->withHeaders(['X-API-Key' => $this->encryptionApiKey()])
                    ->post($this->encryptionUrl() . '/encrypt', [
                        'message' => $messageContent
                    ]);
    
                if ($encryptionResponse->failed()) {
                    throw new \Exception('Message encryption failed');
                }
    
                $encryptedData = $encryptionResponse->json();
                $encryptedMessage = $encryptedData['encrypted_message'];
                $iv = $encryptedData['iv'];
                $kyber_ciphertext = $encryptedData['kyber_ciphertext'];
                $tag = $encryptedData['tag'];
                $hkdf_salt = $encryptedData['hkdf_salt'];
                $kem_private_key = $encryptedData['kem_private_key'];
            }
    
            $message = new PrivateMessage();
            $message->chat_id = $validated['chat_id'];
            $message->sender_id = auth()->id();
            $message->encrypted_message = $encryptedMessage;
            $message->iv = $iv;
            $message->kyber_ciphertext = $kyber_ciphertext;
            $message->tag = $tag;
            $message->hkdf_salt = $hkdf_salt;
            $message->kem_private_key = $kem_private_key;
            $message->media_path = $mediaPath;
            $message->media_type = $mediaType;
            $message->save();
    
            // Broadcast only metadata — no plaintext or encrypted content
            broadcast(new \App\Events\PrivateMessageSent(
                $message->id,
                $message->chat_id,
                auth()->user()->name,
                $mediaPath,
                $mediaType,
                $message->created_at->toIso8601String(),
                $request->input('client_id')
            ))->toOthers();
    
            return response()->json(array_merge($message->toArray(), [
                'client_id' => $request->input('client_id')
            ]));
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Message sending failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMessages($chatId)
    {
        // Authorization: verify user is a participant
        $chat = PrivateChat::findOrFail($chatId);
        if (!$this->authorizeChat($chat)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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
                    'content' => '', 
                    'media_path' => $msg->media_path,
                    'media_type' => $msg->media_type,
                    'created_at' => $msg->created_at
                ];
            }
    
            // Only attempt decryption if encrypted content exists
            $decryptedContent = '';
            if (!empty($msg->encrypted_message)) {
                $response = Http::withoutVerifying()
                    ->withHeaders(['X-API-Key' => $this->encryptionApiKey()])
                    ->post($this->encryptionUrl() . '/decrypt', [
                        'kyber_ciphertext' => $msg->kyber_ciphertext,
                        'encrypted_message' => $msg->encrypted_message,
                        'iv' => $msg->iv,
                        'tag' => $msg->tag ?? '',
                        'hkdf_salt' => $msg->hkdf_salt ?? '',
                        'kem_private_key' => $msg->kem_private_key ?? '',
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

    /**
     * Fetch a single private message by ID (for real-time fetch after broadcast).
     */
    public function getMessage($chatId, $messageId)
    {
        $chat = PrivateChat::findOrFail($chatId);
        if (!$this->authorizeChat($chat)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $msg = PrivateMessage::with('sender')
            ->where('chat_id', $chatId)
            ->where('id', $messageId)
            ->firstOrFail();

        if (empty($msg->encrypted_message)) {
            return response()->json([
                'id' => $msg->id,
                'sender_name' => $msg->sender->name,
                'content' => '',
                'media_path' => $msg->media_path,
                'media_type' => $msg->media_type,
                'created_at' => $msg->created_at
            ]);
        }

        $response = Http::withoutVerifying()
            ->withHeaders(['X-API-Key' => $this->encryptionApiKey()])
            ->post($this->encryptionUrl() . '/decrypt', [
                'kyber_ciphertext' => $msg->kyber_ciphertext,
                'encrypted_message' => $msg->encrypted_message,
                'iv' => $msg->iv,
                'tag' => $msg->tag ?? '',
                'hkdf_salt' => $msg->hkdf_salt ?? '',
                'kem_private_key' => $msg->kem_private_key ?? '',
            ]);

        $decryptedContent = $response->successful()
            ? $response->json()['decrypted_message']
            : 'Decryption failed';

        return response()->json([
            'id' => $msg->id,
            'sender_name' => $msg->sender->name,
            'content' => $decryptedContent,
            'media_path' => $msg->media_path,
            'media_type' => $msg->media_type,
            'created_at' => $msg->created_at
        ]);
    }

    public function listChats()
    {
        $user = auth()->user();
        $chats = PrivateChat::with(['userOne', 'userTwo', 'messages' => function($query) {
            $query->latest('created_at')->limit(1);
        }])
        ->where(function($query) use ($user) {
            $query->where('user_one_id', $user->id)
                  ->orWhere('user_two_id', $user->id);
        })
        ->get()
        ->map(function($chat) use ($user) {
            $otherUser = $chat->user_one_id === $user->id ? $chat->userTwo : $chat->userOne;
            $lastMessage = $chat->messages->first();

            return [
                'id' => $chat->id,
                'other_user' => [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name
                ],
                'last_message' => $lastMessage ? [
                    'content' => '🔒 Encrypted message',
                    'created_at' => $lastMessage->created_at
                ] : null,
                'updated_at' => $lastMessage ? $lastMessage->created_at : $chat->created_at,
                'unread_count' => $chat->messages()
                    ->where('sender_id', '!=', $user->id)
                    ->whereNull('read_at')
                    ->count()
            ];
        });

        return response()->json($chats);
    }
}
