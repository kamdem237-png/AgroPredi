<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScanController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Routes de diagnostic (PyTorch + Flask)
Route::prefix('scan')->middleware('throttle:30,1')->group(function () {
    Route::get('/', [ScanController::class, 'scan'])->name('scan.upload');
    Route::post('/analyze', [ScanController::class, 'analyze'])->name('scan.analyze');
    Route::get('/history', [ScanController::class, 'history'])->name('scan.history');
    Route::get('/stats', [ScanController::class, 'stats'])->name('scan.stats');
    Route::get('/{id}', [ScanController::class, 'show'])->name('scan.show');
    Route::delete('/{id}', [ScanController::class, 'destroy'])->name('scan.destroy');
});