<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\HistoryController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('history.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Chat Routes
    Route::get('/chat', [\App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');

    // History Routes
    Route::resource('history', \App\Http\Controllers\HistoryController::class);

    // Additional history routes
    Route::get('history/{history}/display', [\App\Http\Controllers\HistoryController::class, 'display'])->name('history.display');
    Route::get('history/{history}/chart', [\App\Http\Controllers\HistoryController::class, 'chart'])->name('history.chart');
    Route::get('history/{history}/chart-details', [\App\Http\Controllers\HistoryController::class, 'chartDetails'])->name('history.chart-details');
    Route::get('history/{history}/export', [\App\Http\Controllers\HistoryController::class, 'export'])->name('history.export');
    Route::get('history/{history}/subdashboard/{filter_column?}/{filter_value?}', [\App\Http\Controllers\HistoryController::class, 'subdashboard'])->name('history.subdashboard');
    Route::post('history/{history}/clone', [\App\Http\Controllers\HistoryController::class, 'clone'])->name('history.clone');
});


// Social Auth Routes
Route::get('/auth/{provider}', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToProvider'])
    ->name('auth.social');

Route::get('/auth/{provider}/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'handleProviderCallback'])
    ->name('auth.social.callback');
