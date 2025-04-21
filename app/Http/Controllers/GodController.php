<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use Inertia\Inertia;
use Illuminate\Support\Carbon;
use App\Http\Controllers\ChatRoomController;

class GodController extends Controller {
    public function showAuth() {
        $user = auth()->user();
        $banKey = 'hell_pass_banned_until_'.$user->id;

        if ( $bannedUntil = session( $banKey ) ) {
            if ( Carbon::now()->lessThan( $bannedUntil ) ) {
                $remaining = Carbon::now()->diffInMinutes( $bannedUntil );
                return Inertia::render( 'God/Auth', [
                    'errors' => [ 'hell_pass' => "You have been banished. Return in $remaining minutes." ]
                ] );
            }
            session()->forget( [ $banKey, 'hell_pass_attempts_'.$user->id ] );
        }

        if ( session( 'hell_authenticated' ) ) {
            return redirect()->route( 'hell.board' );
        }

        return Inertia::render( 'God/Auth' );
    }

    public function authenticate( Request $request ) {
        $request->validate( [ 'hell_pass' => 'required|string' ] );
        $user = auth()->user();
        $maxAttempts = 3;
        $banMinutes = 30;
        $attemptKey = 'hell_pass_attempts_'.$user->id;
        $banKey = 'hell_pass_banned_until_'.$user->id;

        if ( $bannedUntil = session( $banKey ) ) {
            if ( Carbon::now()->lessThan( $bannedUntil ) ) {
                $remaining = Carbon::now()->diffInMinutes( $bannedUntil );
                return back()->withErrors( [ 'hell_pass' => "Banished to the void. Return in $remaining minutes." ] );
            }
            session()->forget( [ $attemptKey, $banKey ] );
        }

        if ( $request->hell_pass !== $user->hell_pass ) {
            $attempts = session( $attemptKey, 0 ) + 1;
            session( [ $attemptKey => $attempts ] );

            if ( $attempts >= $maxAttempts ) {
                session( [ $banKey => Carbon::now()->addMinutes( $banMinutes ) ] );
                return back()->withErrors( [
                    'hell_pass' => "Too many failures. Banished for $banMinutes minutes."
                ] );
            }

            $remainingAttempts = $maxAttempts - $attempts;
            return back()->withErrors( [
                'hell_pass' => "Invalid passphrase. $remainingAttempts attempts remain."
            ] );
        }

        session()->forget( [ $attemptKey, $banKey ] );
        session( [ 'hell_authenticated' => true ] );
        return redirect()->route( 'hell.board' );
    }

    public function dashboard() {
        $user = auth()->user();
        $banKey = 'hell_pass_banned_until_'.$user->id;

        if ( $bannedUntil = session( $banKey ) ) {
            if ( Carbon::now()->lessThan( $bannedUntil ) ) {
                session()->forget( 'hell_authenticated' );
                return redirect()->route( 'god.board.auth' )->withErrors( [
                    'hell_pass' => 'You are currently banished from the void.'
                ] );
            }
            session()->forget( [ $banKey, 'hell_pass_attempts_'.$user->id ] );
        }

        if ( !session( 'hell_authenticated' ) ) {
            return redirect()->route( 'god.board.auth' );
        }

        return inertia( 'God/Dashboard', [
            'stats' => [
                [ 'label' => 'Damned Souls', 'value' => User::count() ],
                [ 'label' => 'Chambers of Torment', 'value' => ChatRoom::count() ],
                [ 'label' => 'Soul Screams', 'value' => ChatMessage::count() ]
            ]
        ] );
    }

    public function users( Request $request ) {
        $user = auth()->user();
        $banKey = 'hell_pass_banned_until_'.$user->id;

        if ( $bannedUntil = session( $banKey ) ) {
            if ( Carbon::now()->lessThan( $bannedUntil ) ) {
                session()->forget( 'hell_authenticated' );
                return redirect()->route( 'god.board.auth' )->withErrors( [
                    'hell_pass' => 'You are currently banished from the void.'
                ] );
            }
            session()->forget( [ $banKey, 'hell_pass_attempts_'.$user->id ] );
        }

        if ( !session( 'hell_authenticated' ) ) {
            return redirect()->route( 'god.board.auth' );
        }

        $users = User::when( $request->search, fn( $q, $search ) => $q->search( $search ) )
        ->paginate( 10 );

        return Inertia::render( 'God/Users', [ 'users' => $users ] );
    }

    public function chatrooms( Request $request ) {
        $user = auth()->user();
        $banKey = 'hell_pass_banned_until_'.$user->id;

        if ( $bannedUntil = session( $banKey ) ) {
            if ( Carbon::now()->lessThan( $bannedUntil ) ) {
                session()->forget( 'hell_authenticated' );
                return redirect()->route( 'god.board.auth' )->withErrors( [
                    'hell_pass' => 'You are currently banished from the void.'
                ] );
            }
            session()->forget( [ $banKey, 'hell_pass_attempts_'.$user->id ] );
        }

        if ( !session( 'hell_authenticated' ) ) {
            return redirect()->route( 'god.board.auth' );
        }

        $chatrooms = ChatRoom::query()
        ->search( $request->search )
        ->paginate( 10 )
        ->withQueryString();

        return Inertia::render( 'God/Chatrooms', [
            'chatrooms' => $chatrooms,
            'filters' => $request->only( [ 'search' ] )
        ] );

    }
}