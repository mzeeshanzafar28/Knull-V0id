<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FileController;


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

//404 page
Route::fallback(function () {
    return Inertia::render('Errors/404',['title' => '404 - Page Not Found']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    //Chat Rooms Routes
    Route::get('/chat-rooms', function () {
        return Inertia::render('ChatRooms/ListRooms');
    })->name('listrooms');
    Route::post('/chat-rooms', [ChatController::class, 'listRooms']); //return all chatrooms
    Route::post('/chat/{roomId}/join', [ChatController::class, 'joinRoom']);
    Route::post('/chat/{roomId}/send', [ChatController::class, 'sendMessage']);
    
    // File Transfer Routes
    Route::get('/files', function () {
        return Inertia::render('Files/FilesSection');
    })->name('filessection');
    Route::get('/files/upload', function () {
        return Inertia::render('Files/FilesUpload');
    })->name('filesupload');
    Route::get('/files/download', function () {
        return Inertia::render('Files/FilesDownload');
    })->name('filesdownload');
    Route::post('/files/upload', [ChatController::class, 'upload']);
    Route::post('/files/download', [ChatController::class, 'download']);
    


});