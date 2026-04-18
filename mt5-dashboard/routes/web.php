<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// API routes for MT5 EA and frontend
Route::prefix('api')->group(function () {
    Route::post('/drawdown', [DashboardController::class, 'store'])->name('api.drawdown.store');
    Route::get('/drawdowns/top-10', [DashboardController::class, 'top10'])->name('api.drawdowns.top10');
    Route::get('/drawdowns/current-month', [DashboardController::class, 'currentMonth'])->name('api.drawdowns.current-month');
    Route::get('/drawdowns/all', [DashboardController::class, 'all'])->name('api.drawdowns.all');
});
