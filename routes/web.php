<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PrivateChatController;
use App\Http\Controllers\FileController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Middleware\GodUserMiddleware;
use App\Http\Controllers\GodController;
use App\Http\Controllers\ChatRoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvestigationController;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

//God User Management
Route::middleware(['auth', GodUserMiddleware::class])->group(function () {
    Route::get('/god-board', [GodController::class, 'showAuth'])->name('god.board.auth');
    Route::post('/god-board/authenticate', [GodController::class, 'authenticate'])->name('god.board.authenticate');
    Route::get('/hell-board', [GodController::class, 'dashboard'])->name('hell.board');
    Route::get('/hell-board/users', [GodController::class, 'users'])->name('hell.users');
    Route::get('/hell-board/chatrooms', [GodController::class, 'chatrooms'])->name('hell.chatrooms');
    
    Route::resource('users', UserController::class)->except(['show']);

    Route::resource('chatrooms', ChatRoomController::class)->except(['show', 'index']);
    Route::get('/hell-board/chatrooms', [GodController::class, 'chatrooms'])
        ->name('hell.chatrooms');
    
    Route::get('/investigate-user/{user}', [InvestigationController::class, 'show'])->name('investigate.user');
});

// Require authentication and email verification for dashboard access
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

// Email Verification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', function () {
        return Inertia::render('Auth/VerifyEmail');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/dashboard');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// 404 Page
Route::fallback(function () {
    return Inertia::render('Errors/404', ['title' => '404 - Page Not Found']);
});

// Protected routes requiring authentication **but NOT email verification**
Route::middleware(['auth:sanctum'])->group(function () {
    // Chat Rooms Routes
    Route::get('/chat-rooms', function () {
        return Inertia::render('ChatRooms/ListRooms');
    })->name('listrooms');
    Route::post('/chat-rooms', [ChatRoomController::class, 'listRooms']);
    Route::get('/chat/{roomId}/join', [ChatRoomController::class, 'joinRoom']);
    Route::post('/chat/{roomId}/leave', [ChatRoomController::class, 'leaveRoom']);

    // Chat Messages Routes
    Route::post('/chat/{roomId}/send', [ChatController::class, 'sendMessage']);
    Route::get('/chat/{roomId}/messages', [ChatController::class, 'fetchMessages']);
    Route::get('/chat/{roomId}/members', [ChatController::class, 'fetchMembers']);

    //Media Download Route
    Route::get('/media/{filename}', function ($filename) {
        $path = storage_path('app/public/chat_media/' . $filename);

        if (!Storage::exists('public/chat_media/' . $filename)) {
            abort(404);
        }

        return response()->file($path);
    })->name('media.view');

    //Private Chat Route
    Route::get('/private/message/{name}', [PrivateChatController::class, 'createChat']);
    Route::get('/private/get/{chatId}', [PrivateChatController::class, 'getMessages']);
    Route::post('/private/message/send', [PrivateChatController::class, 'sendMessage']);

    Route::get('/inbox', function () {
        return Inertia::render('Inbox');
    });
    Route::post('/inbox', [PrivateChatController::class, 'listChats']);


    // File Transfer Routes
    Route::get('/files', function () {
        return Inertia::render('Files/FilesSection');
    })->name('filesSection');
    Route::get('/files/upload', function () {
        return Inertia::render('Files/FilesUpload');
    })->name('filesUpload');
    Route::get('/files/download', function () {
        return Inertia::render('Files/FilesDownload');
    })->name('filesDownload');
    Route::post('/files/mine', [FileController::class, 'userFiles']);
    Route::post('/files/upload', [FileController::class, 'upload']);
    Route::post('/files/download', [FileController::class, 'download']);
    Route::post('/files/delete/{file}', [FileController::class, 'delete']);
});
