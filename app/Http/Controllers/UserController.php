<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller {
    public function create() {
        return Inertia::render( 'God/UserForm' );
    }

    public function store( Request $request ) {
        $validated = $request->validate( [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            'is_god_user' => 'boolean'
        ] );

        User::create( $validated );

        return redirect()->route( 'hell.users' )->with( 'message', 'Soul bound to the void!' );
    }

    public function edit( User $user ) {
        return Inertia::render( 'God/UserForm', [
            'user' => $user
        ] );
    }

    public function update( Request $request, User $user ) {
        if ( $user->id === auth()->id() ) {
            return back()->withErrors( [ 'god' => 'You cannot modify your own essence' ] );
        }

        $validated = $request->validate( [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|confirmed|min:8',
            'is_god_user' => 'boolean'
        ] );

        if ( !$validated[ 'password' ] ) {
            unset( $validated[ 'password' ] );
        }

        $user->update( $validated );

        return redirect()->route( 'hell.users' )->with( 'message', 'Soul essence twisted!' );
    }

    public function destroy( User $user ) {
        if ( $user->id === auth()->id() ) {
            return back()->withErrors( [ 'god' => 'You cannot banish your own soul' ] );
        }

        $user->delete();
        return back()->with( 'message', 'Soul banished to the void!' );
    }
}