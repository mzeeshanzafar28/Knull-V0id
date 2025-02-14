<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\File;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        // Add your scheduled tasks here.

        $schedule->call(function () {
            File::where('expires_at', '<=', now())->delete();
        })->daily();


        $schedule->call(function () {
            $ephemeralRooms = ChatRoom::where('is_ephemeral', true)->get();

            foreach ($ephemeralRooms as $room) {
                $threshold = Carbon::now()->subHours($room->self_destruct_hours);

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
