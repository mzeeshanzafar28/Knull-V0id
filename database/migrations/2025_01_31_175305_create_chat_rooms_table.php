<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description');
            $table->unsignedInteger('max_members')->default(100);
            $table->boolean('is_ephemeral')->default(false);
            $table->unsignedInteger('self_destruct_hours')->nullable();
            $table->timestamps();
        });

        // Seed initial rooms
        DB::table('chat_rooms')->insert([
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
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('chat_rooms');
    }
};
