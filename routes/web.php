<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('react', ['page' => 'home']);
})->name('home');

// Dashboard utilisateur (protégé)
Route::middleware(['auth', 'notbanned'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/history', [DashboardController::class, 'history'])->name('dashboard.history');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', function () {
        return view('react', ['page' => 'admin']);
    })->name('admin');

    Route::get('/admin/diagnostics', function () {
        return view('react', ['page' => 'admin_diagnostics']);
    })->name('admin.diagnostics');

    Route::prefix('admin/api')->group(function () {
        Route::get('/overview', [\App\Http\Controllers\AdminController::class, 'overview']);
        Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users']);
        Route::get('/users/{id}/diagnostics', [\App\Http\Controllers\AdminController::class, 'userDiagnostics']);
        Route::post('/users/{id}/toggle-ban', [\App\Http\Controllers\AdminController::class, 'toggleBan']);
        Route::get('/diagnostics', [\App\Http\Controllers\AdminController::class, 'diagnostics']);
    });
});

// Routes de diagnostic (PyTorch + Flask)
Route::prefix('scan')->middleware('throttle:30,1')->group(function () {
    Route::get('/', [ScanController::class, 'scan'])->name('scan.upload');
    Route::post('/analyze', [ScanController::class, 'analyze'])->name('scan.analyze');

    Route::get('/result/{id}', [ScanController::class, 'result'])->name('scan.result');
    Route::get('/result/{id}/pdf', [ScanController::class, 'resultPdf'])->name('scan.result.pdf');

    // Historique legacy : rendu personnel et protégé (évite fuite des diagnostics d'autres utilisateurs)
    Route::get('/history', [ScanController::class, 'history'])->middleware('auth')->name('scan.history');

    Route::get('/stats', [ScanController::class, 'stats'])->name('scan.stats');
    Route::get('/{id}', [ScanController::class, 'show'])->name('scan.show');
    Route::delete('/{id}', [ScanController::class, 'destroy'])->name('scan.destroy');
});

require __DIR__.'/auth.php';

Route::get('/{any}', function () {
    return view('react');
})->where('any', '.*');
