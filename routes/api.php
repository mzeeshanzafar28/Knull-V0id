<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EncryptionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/encrypt', [EncryptionController::class, 'encrypt']);
Route::post('/decrypt', [EncryptionController::class, 'decrypt']);
