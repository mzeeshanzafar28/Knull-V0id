<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyUsersSeeder extends Seeder {
    public function run(): void {
        DB::table( 'users' )->insertOrIgnore( [
            [
                'name' => 'generalzodx2811',
                'email' => 'generalzodx2811@gmail.com',
                'password' => Hash::make( 'SuperSecurePass123' ),
                'is_god_user' => 1,
                'hell_pass' => 'I AM THE CREATOR OF THIS HELL...',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'mzeeshanzafar28',
                'email' => 'mzeeshanzafar28@gmail.com',
                'password' => Hash::make( 'SuperSecurePass123' ),
                'is_god_user' => 1,
                'hell_pass' => 'I AM THE CREATOR OF THIS HELL...',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'regularuser',
                'email' => 'regularuser@example.com',
                'password' => Hash::make( 'UserPass123' ),
                'is_god_user' => 0,
                'hell_pass' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ] );
    }
}