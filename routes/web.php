<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::get('/', function () {
    return redirect()->route('chat');
});

// Chat Interface Routes
Route::get('/chat', [ChatController::class, 'index'])->name('chat');
Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
