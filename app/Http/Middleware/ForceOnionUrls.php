<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceOnionUrls {
    /**
    * Handle an incoming request.
    *
    * @param  \Closure( \Illuminate\Http\Request ): ( \Symfony\Component\HttpFoundation\Response )  $next
    */

    public function handle( $request, Closure $next ) : Response {
        if ( !str_contains( $request->getHost(), '.onion' ) ) {
            return redirect()->away( env( 'APP_URL' ) );
        }

        return $next( $request )->withHeaders( [
            'Content-Security-Policy' => "default-src 'self' http://".env( 'VITE_APP_ONION_DOMAIN' ),
            'Access-Control-Allow-Origin' => env( 'APP_URL' ),
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
        ] );
    }
}
