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
            $mediaPath = null;
            $mediaType = null;
            $messageContent = $validated['message'] ?? 'Media shared';

            if ($request->hasFile('media')) {
                $file = $request->file('media');
                $mediaPath = $file->store('chat_media', 'public');
                $mediaType = $file->getMimeType();

                // If media is uploaded, override any text message
                $messageContent = '';
            }

            // Only encrypt if there's a text message
            $encryptedMessage = null;
            $iv = null;
            $kyber_ciphertext = null;

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

            $message = ChatMessage::create([
                'chat_room_id' => $roomId,
                'user_id' => auth()->id(),
                'encrypted_message' => $encryptedMessage,
                'kyber_ciphertext' => $kyber_ciphertext,
                'iv' => $iv,
                'sender' => auth()->user()->name,
                'media_path' => $mediaPath,
                'media_type' => $mediaType
            ]);

            event(new NewChatMessage(
                $roomId,
                $encryptedMessage,
                $messageContent,
                auth()->user()->name,
                $mediaPath,
                $mediaType
            ));

            return response()->json([
                'status' => 'Message sent',
                'sender' => auth()->user()->name,
                'media_path' => $mediaPath
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Media upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch all messages for a specific chat room.
     */
    public function fetchMessages(Request $request, $roomId){
        $messages = ChatMessage::where('chat_room_id', $roomId)
                    ->orderBy('created_at', 'asc')
                    ->get();



        $decryptedMessages = $messages->map(function ($msg) {
            $response = Http::withoutVerifying()->post('https://127.0.0.1:5000/decrypt', [
                'kyber_ciphertext' => $msg->kyber_ciphertext,
                'encrypted_message' => $msg->encrypted_message,
                'iv' => $msg->iv,
            ]);
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
