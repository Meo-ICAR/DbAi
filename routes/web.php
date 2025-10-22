<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\HistoryController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
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

    // User Role Management Routes
    Route::prefix('users/roles')->name('admin.users.roles.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\UserRoleController::class, 'index'])->name('index');
        Route::put('/{user}', [\App\Http\Controllers\Admin\UserRoleController::class, 'update'])->name('update');
        Route::get('/bulk-assign', [\App\Http\Controllers\Admin\UserRoleController::class, 'bulkAssign'])->name('bulk-assign');
        Route::post('/bulk-assign', [\App\Http\Controllers\Admin\UserRoleController::class, 'processBulkAssign'])->name('process-bulk-assign');
    });

    // Role Management Routes
    Route::prefix('roles')->name('admin.roles.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('store');
        Route::put('/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('destroy');
    });

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
