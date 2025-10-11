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
Route::prefix('history')->name('history.')->group(function () {
    Route::get('/', [HistoryController::class, 'index'])->name('index');
    Route::get('/create', [HistoryController::class, 'create'])->name('create');
    Route::post('/', [HistoryController::class, 'store'])->name('store');
    Route::get('/{history}/edit', [HistoryController::class, 'edit'])->name('edit');
    Route::put('/{history}', [HistoryController::class, 'update'])->name('update');
    Route::delete('/{history}', [HistoryController::class, 'destroy'])->name('destroy');
    Route::get('/{history}', [HistoryController::class, 'show'])->name('show');
    Route::get('/{history}/display', [HistoryController::class, 'display'])->name('display');
    Route::post('/{history}/clone', [HistoryController::class, 'clone'])->name('clone');
    Route::get('/{history}/chart', [HistoryController::class, 'chart'])->name('chart');
    Route::post('/{history}/update-order', [HistoryController::class, 'updateOrder'])->name('update-order');
});

// Dashboard Route
Route::get('/dashboard', [HistoryController::class, 'dashboard'])->name('dashboard');
