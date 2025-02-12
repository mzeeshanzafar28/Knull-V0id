<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class BroadcastServiceProvider extends ServiceProvider {
    /**
    * Register services.
    */

    public function register(): void {
        //
    }

    /**
    * Bootstrap services.
    */

    public function boot(): void {
        Broadcast::routes( [ 'middleware' => [ 'web', 'auth:sanctum' ] ] );

        require base_path( 'routes/channels.php' );
    }
}
