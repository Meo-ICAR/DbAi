<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

// Welcome route with language detection
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Language switcher route
Route::get('/locale/{locale}', [WelcomeController::class, 'setLocale'])->name('locale.set');

Route::get('/dashboard', [\App\Http\Controllers\HistoryController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Apply auth middleware to all routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User Management Routes
    Route::resource('users', \App\Http\Controllers\UserController::class)->except(['show']);

    // Chat Routes
    Route::get('/chat', [\App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');

    // Company Management Routes
    Route::resource('companies', \App\Http\Controllers\Admin\CompanyController::class)->names('admin.companies');


    // History Routes
    Route::resource('history', \App\Http\Controllers\HistoryController::class);

    // Tables Route
    Route::get('/tables', [\App\Http\Controllers\HistoryController::class, 'tables'])->name('tables');

    // Additional history routes
    Route::get('history/{history}/display', [\App\Http\Controllers\HistoryController::class, 'display'])->name('history.display');
    Route::get('history/{history}/chart', [\App\Http\Controllers\HistoryController::class, 'chart'])->name('history.chart');
    Route::get('history/{history}/chart-details', [\App\Http\Controllers\HistoryController::class, 'chartDetails'])->name('history.chart-details');
    Route::get('history/{history}/export', [\App\Http\Controllers\HistoryController::class, 'export'])->name('history.export');
    Route::get('history/{history}/subdashboard/{filter_column?}/{filter_value?}', [\App\Http\Controllers\HistoryController::class, 'subdashboard'])->name('history.subdashboard');
    Route::post('history/{history}/clone', [\App\Http\Controllers\HistoryController::class, 'clone'])->name('history.clone');
    Route::get('/history/tables', [\App\Http\Controllers\HistoryController::class, 'tables'])->name('history.tables');
    
    // Chat History Management Routes
    Route::resource('chat-history', \App\Http\Controllers\ChatHistoryController::class)
        ->parameters(['chat-history' => 'chat_history'])
        ->names('chat-history');
 
});


// Social Auth Routes
Route::get('/auth/{provider}', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToProvider'])
    ->name('auth.social');

Route::get('/auth/{provider}/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'handleProviderCallback'])
    ->name('auth.social.callback');
