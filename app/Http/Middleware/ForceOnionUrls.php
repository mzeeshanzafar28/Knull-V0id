<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceOnionUrls {
    public function handle( Request $request, Closure $next ): Response {
        if ( !str_contains( $request->getHost(), '.onion' ) ) {
            return redirect()->away( env( 'APP_URL' ) );
        }

        $onionDomain = env( 'VITE_APP_ONION_DOMAIN' );

        $response = $next( $request );

        $response->headers->add( [
            'Content-Security-Policy' => implode( '; ', [
                "default-src 'self'",
                "img-src 'self' data:",
                "media-src 'self' data:",
                "font-src 'self' https://fonts.bunny.net https://fonts.gstatic.com",
                "script-src 'self' 'unsafe-inline' http://{$onionDomain}:5173",
                "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://fonts.googleapis.com",
                "connect-src 'self' ws://{$onionDomain}:5173 ws://{$onionDomain}:8080"
            ] ),
            'Access-Control-Allow-Origin' => env( 'APP_URL' ),
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
        ] );

        return $response;
    }
}