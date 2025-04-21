<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatRoomsSeeder extends Seeder {
    /**
    * Run the database seeds.
    */

    public function run(): void {
        // Seed initial rooms
        DB::table( 'chat_rooms' )->insert( [
            [
                'name' => 'The Wailing Chamber',
                'description' => 'General discussions in the void',
                'max_members' => 100,
                'is_ephemeral' => true,
                'self_destruct_hours' => 12,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'CS Crypts',
                'description' => 'Computer Science shadows gather here',
                'max_members' => 50,
                'is_ephemeral' => true,
                'self_destruct_hours' => 48,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Quantum Catacombs',
                'description' => 'Cryptographic rituals and dark mathematics',
                'max_members' => 25,
                'is_ephemeral' => true,
                'self_destruct_hours' => 48,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'AI Abyss',
                'description' => 'AI and ML punks reside here',
                'max_members' => 25,
                'is_ephemeral' => true,
                'self_destruct_hours' => 48,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Cyber Valley',
                'description' => 'Cyber Security Kangaros live down here',
                'max_members' => 25,
                'is_ephemeral' => true,
                'self_destruct_hours' => 24,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Money Heists',
                'description' => 'Money Scrapers faint ly whisper here',
                'max_members' => 25,
                'is_ephemeral' => true,
                'self_destruct_hours' => 24,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'The crown of Zod',
                'description' => 'Get to meet the Creator of this hell here',
                'max_members' => 2,
                'is_ephemeral' => true,
                'self_destruct_hours' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ] );
    }
}
