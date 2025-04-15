<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\File;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Existing file cleanup
        $schedule->call(function () {
            File::where('expires_at', '<=', now())->delete();
        })->daily();
    
        // Enhanced ephemeral room cleanup
        $schedule->call(function () {
            $ephemeralRooms = ChatRoom::where('is_ephemeral', true)->get();
    
            foreach ($ephemeralRooms as $room) {
                $threshold = Carbon::now()->subHours($room->self_destruct_hours);
                
                // Get messages scheduled for deletion
                $messages = ChatMessage::where('chat_room_id', $room->id)
                    ->where('created_at', '<', $threshold)
                    ->get();
    
                // Delete associated media files
                foreach ($messages as $message) {
                    if ($message->media_path) {
                        Storage::disk('public')->delete($message->media_path);
                    }
                }
    
                // Delete messages
                ChatMessage::where('chat_room_id', $room->id)
                    ->where('created_at', '<', $threshold)
                    ->delete();
            }
        })->everyFiveMinutes();
    }


    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
