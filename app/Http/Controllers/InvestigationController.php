<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use Inertia\Inertia;
use App\Http\Controllers\UserController;
use App\Models\User;

class InvestigationController extends Controller {
    public function show( User $user ) {
        $messages = ChatMessage::where( 'user_id', $user->id )
        ->selectRaw( 'chat_room_id, count(*) as count' )
        ->groupBy( 'chat_room_id' )
        ->with( 'room' )
        ->get();

        return Inertia::render( 'God/Investigation', [
            'user' => $user,
            'messages' => $messages,
            'total' => $messages->sum( 'count' )
        ] );
    }
}
