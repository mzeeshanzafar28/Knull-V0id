<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class EnsureEmailIsVerified {
    public function handle( Request $request, Closure $next ) {
        if ( !$request->user() || !$request->user()->hasVerifiedEmail() ) {
            return Redirect::route( 'verification.notice' );
        }

        return $next( $request );
    }
}
