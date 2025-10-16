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
    Route::get('/{history}/display/{filter_column?}/{filter_value?}', [HistoryController::class, 'display'])
     //   ->where(['filter_value' => '.*'])
        ->name('display');

    Route::get('/{history}/chart/{filter_column?}/{filter_value?}', [HistoryController::class, 'chart'])
      //  ->where(['filter_value' => '.*'])
        ->name('chart');

    Route::post('/{history}/clone', [HistoryController::class, 'clone'])->name('clone');
    Route::get('/{history}/chart-details', [HistoryController::class, 'chartDetails'])->name('chart-details');
    Route::post('/{history}/update-order', [HistoryController::class, 'updateOrder'])->name('update-order');

    // Export to Excel route with filter parameters
    Route::get('/{history}/export/{filter_column?}/{filter_value?}', function (\App\Models\History $history, $filter_column = null, $filter_value = null) {
        $request = request();
        $request->merge([
            'export' => true,
            'filter_column' => $filter_column,
            'filter_value' => $filter_value
        ]);
        return app(HistoryController::class)->display($history, $request, $filter_column, $filter_value);
    })
    //->where(['filter_value' => '.*'])
    ->name('export');

    // Subdashboard route
    Route::get('/{history}/subdashboard/{filter_column?}/{filter_value?}', [HistoryController::class, 'subdashboard'])
        ->where(['filter_value' => '.*'])
        ->name('subdashboard');
});

// Dashboard Route
Route::get('/dashboard', [HistoryController::class, 'dashboard'])->name('dashboard');
