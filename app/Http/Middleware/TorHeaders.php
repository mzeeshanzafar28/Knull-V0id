<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TorHeaders {
    /**
    * Handle an incoming request.
    *
    * @param  \Closure( \Illuminate\Http\Request ): ( \Symfony\Component\HttpFoundation\Response )  $next
    */

    public function handle( $request, Closure $next ) : Response {
        $response = $next( $request );

        $appUrl = config( 'app.url', '' );

        $response->headers->set( 'Access-Control-Allow-Origin', $appUrl );
        $response->headers->set( 'Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS' );
        $response->headers->set( 'Access-Control-Allow-Headers', 'Content-Type, Authorization' );
        $response->headers->set( 'Onion-Location', $appUrl );

        return $response;
    }
}
