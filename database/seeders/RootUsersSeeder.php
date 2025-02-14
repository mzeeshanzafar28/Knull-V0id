<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RootUsersSeeder extends Seeder {
    public function run() {
        $rootUsers = [
            [
                'name' => 'generalzodx28',
                'email' => 'generalzodx28@gmail.com',
                'password' => Hash::make( 'SuperSecurePass123' ),
            ],
            [
                'name' => 'mzeeshanzafar28',
                'email' => 'mzeeshanzafar28@gmail.com',
                'password' => Hash::make( 'SuperSecurePass123' ),
            ],
        ];

        foreach ( $rootUsers as $userData ) {
            if ( !User::where( 'email', $userData[ 'email' ] )->exists() ) {
                User::create( $userData );
            }
        }
    }
}
