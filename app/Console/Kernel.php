<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\File;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\PrivateMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // File cleanup
        $schedule->call(function () {
            File::where('expires_at', '<=', now())->delete();
        })->daily();

        // Chats media cleanup
        $schedule->call(function () {
            $this->cleanupChatRoomMedia();
            $this->cleanupPrivateChatMedia();
        })->everyFiveMinutes();
    }

    protected function cleanupChatRoomMedia()
    {
        $ephemeralRooms = ChatRoom::where('is_ephemeral', true)->get();

        foreach ($ephemeralRooms as $room) {
            $threshold = Carbon::now()->subHours($room->self_destruct_hours);
            
            $messages = ChatMessage::where('chat_room_id', $room->id)
                ->where('created_at', '<', $threshold)
                ->get();

            foreach ($messages as $message) {
                if ($message->media_path) {
                    Storage::disk('public')->delete($message->media_path);
                }
            }

            ChatMessage::where('chat_room_id', $room->id)
                ->where('created_at', '<', $threshold)
                ->delete();
        }
    }

    protected function cleanupPrivateChatMedia()
    {
        // Cleanup private messages older than 3 days by default
        $threshold = Carbon::now()->subDays(3);
        
        $messages = PrivateMessage::where('created_at', '<', $threshold)->get();

        foreach ($messages as $message) {
            if ($message->media_path) {
                Storage::disk('public')->delete($message->media_path);
            }
        }

        PrivateMessage::where('created_at', '<', $threshold)->delete();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}