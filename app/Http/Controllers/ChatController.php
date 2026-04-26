<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessage;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;


class ChatController extends Controller
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
     * Send a message in a chat room.
     * Encrypts the message via a Python microservice, stores it, and broadcasts it.
     */
    public function sendMessage(Request $request, $roomId)
    {
        try {
            $validated = $request->validate([
                'message' => 'nullable|string',
                'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:25600'
            ]);

            $room = ChatRoom::findOrFail($roomId);

            // Verify user is a member of this room
            $activeUsers = cache()->get("chat_room_users_{$roomId}", []);
            if (!isset($activeUsers[auth()->id()]) && !auth()->user()->is_god_user) {
                return response()->json(['error' => 'You are not a member of this room'], 403);
            }

            $mediaPath = null;
            $mediaType = null;
            $messageContent = $validated['message'] ?? '';

            if ($request->hasFile('media')) {
                $file = $request->file('media');
                $mediaPath = $file->store('chat_media', 'public');
                $mediaType = $file->getMimeType();

                // If media is uploaded, clear text message
                $messageContent = '';
            }

            // Only encrypt if there's a text message
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

            $message = ChatMessage::create([
                'chat_room_id' => $roomId,
                'user_id' => auth()->id(),
                'encrypted_message' => $encryptedMessage,
                'kyber_ciphertext' => $kyber_ciphertext,
                'iv' => $iv,
                'tag' => $tag,
                'hkdf_salt' => $hkdf_salt,
                'kem_private_key' => $kem_private_key,
                'sender' => auth()->user()->name,
                'media_path' => $mediaPath,
                'media_type' => $mediaType
            ]);

            // Broadcast the message (Metadata only for security)
            broadcast(new \App\Events\NewChatMessage(
                $message->id,
                $roomId,
                auth()->user()->name,
                $message->media_path,
                $message->media_type,
                $message->created_at->toIso8601String(),
                $request->input('client_id')
            ))->toOthers();

            return response()->json([
                'status' => 'Message sent into the abyss',
                'message_id' => $message->id,
                'media_path' => $message->media_path,
                'client_id' => $request->input('client_id')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Message sending failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch all messages for a specific chat room.
     * Decrypts each message via the Python microservice.
     */
    public function fetchMessages(Request $request, $roomId){
        // Verify user is a member of this room
        $activeUsers = cache()->get("chat_room_users_{$roomId}", []);
        if (!isset($activeUsers[auth()->id()]) && !auth()->user()->is_god_user) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $messages = ChatMessage::where('chat_room_id', $roomId)
                    ->orderBy('created_at', 'asc')
                    ->get();

        $decryptedMessages = $messages->map(function ($msg) {
            // Handle media-only messages (no encrypted content)
            if (empty($msg->encrypted_message)) {
                return array_merge($msg->toArray(), ['content' => '']);
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

            return array_merge($msg->toArray(), ['content' => $decryptedContent]);
        });

        return response()->json($decryptedMessages);
    }

    /**
     * Fetch a single message by ID (used for real-time message fetching after broadcast).
     */
    public function fetchMessage(Request $request, $roomId, $messageId)
    {
        // Verify user is a member of this room
        $activeUsers = cache()->get("chat_room_users_{$roomId}", []);
        if (!isset($activeUsers[auth()->id()]) && !auth()->user()->is_god_user) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $msg = ChatMessage::where('chat_room_id', $roomId)
            ->where('id', $messageId)
            ->firstOrFail();

        if (empty($msg->encrypted_message)) {
            return response()->json(array_merge($msg->toArray(), ['content' => '']));
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

        return response()->json(array_merge($msg->toArray(), ['content' => $decryptedContent]));
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
