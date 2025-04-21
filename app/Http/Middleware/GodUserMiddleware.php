<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\GodController;
use App\Models\User;

class GodUserMiddleware {
    /**
    * Handle an incoming request.
    *
    * @param  \Closure( \Illuminate\Http\Request ): ( \Symfony\Component\HttpFoundation\Response )  $next
    */

    public function handle( $request, Closure $next ): Response {
        if ( !auth()->check() || !auth()->user()->is_god_user ) {
            abort( 403, 'Access to the dark council denied' );
        }
        return $next( $request );
    }

    // public function authenticate( Request $request ) {
    //     $request->validate( [ 'hell_pass' => 'required|string' ] );

    //     $user = auth()->user();
    //     $maxAttempts = 3;
    //     $banMinutes = 30;

    //     // User-specific session keys
    //     $attemptKey = 'hell_pass_attempts_'.$user->id;
    //     $banKey = 'hell_pass_banned_until_'.$user->id;

    //     // Check if banned
    //     if ( $bannedUntil = session( $banKey ) ) {
    //         if ( now()->lessThan( $bannedUntil ) ) {
    //             $remaining = now()->diffInMinutes( $bannedUntil );
    //             return back()->withErrors( [
    //                 'hell_pass' => "You have been banished to the void. Return in $remaining minutes."
    // ] );
    //         }
    //         session()->forget( [ $attemptKey, $banKey ] );
    //     }

    //     // Check passphrase
    //     if ( $request->hell_pass !== $user->hell_pass ) {
    //         $attempts = session( $attemptKey, 0 ) + 1;
    //         session( [ $attemptKey => $attempts ] );

    //         if ( $attempts >= $maxAttempts ) {
    //             session( [ $banKey => now()->addMinutes( $banMinutes ) ] );
    //             return back()->withErrors( [
    //                 'hell_pass' => "Too many failed attempts. Banished for $banMinutes minutes."
    // ] );
    //         }

    //         $remaining = $maxAttempts - $attempts;
    //         return back()->withErrors( [
    //             'hell_pass' => "Invalid hell passphrase. $remaining attempts remain."
    // ] );
    //     }

    //     // Successful authentication
    //     session()->forget( [ $attemptKey, $banKey ] );
    //     session( [ 'hell_authenticated' => true ] );

    //     return redirect()->route( 'hell.board' );
    // }

}
