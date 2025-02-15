<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FileController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
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
    Route::post('/chat-rooms', [ChatController::class, 'listRooms']);
    Route::get('/chat/{roomId}/join', [ChatController::class, 'joinRoom']);
    Route::post('/chat/{roomId}/send', [ChatController::class, 'sendMessage']);
    Route::get('/chat/{roomId}/messages', [ChatController::class, 'fetchMessages']);
    Route::get('/chat/{roomId}/members', [ChatController::class, 'fetchMembers']);
    Route::post('/chat/{roomId}/leave', [ChatController::class, 'leaveRoom']);

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
