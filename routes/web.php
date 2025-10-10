<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\HistoryController;

Route::get('/', function () {
    return redirect()->route('chat');
});

// Chat Interface Routes
Route::get('/chat', [ChatController::class, 'index'])->name('chat');
Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
Route::get('/chat/chart', [ChatController::class, 'showChart'])->name('chat.chart');

// History Routes
Route::resource('history', HistoryController::class)->except(['show']);
Route::get('history/{history}', [HistoryController::class, 'show'])->name('history.show');
Route::get('history/{history}/display', [HistoryController::class, 'display'])->name('history.display');
Route::get('history/{history}/chart', [HistoryController::class, 'chart'])->name('history.chart');
