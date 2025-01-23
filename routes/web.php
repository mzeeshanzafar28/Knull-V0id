<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/chat-rooms', [ChatRoomController::class, 'index'])->name('chat.rooms');
    Route::post('/chat-rooms', [ChatRoomController::class, 'create']);
    Route::get('/chat-rooms/{id}', [ChatRoomController::class, 'show']);
    Route::post('/chat-rooms/{id}/messages', [ChatRoomController::class, 'sendMessage']);

    // File transfer routes
    Route::get('/files', [FileTransferController::class, 'index'])->name('files.index');
    Route::post('/files/upload', [FileTransferController::class, 'upload'])->name('files.upload');
    Route::get('/files/download/{id}', [FileTransferController::class, 'download'])->name('files.download');
});