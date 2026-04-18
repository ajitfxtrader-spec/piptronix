<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {
    // Endpoint for MT5 EA to send drawdown data
    Route::post('/drawdown', [DashboardController::class, 'storeDrawdown'])
        ->name('api.drawdown.store');
    
    // Get top 10 largest drawdowns
    Route::get('/drawdowns/top-10', [DashboardController::class, 'getTop10'])
        ->name('api.drawdowns.top10');
    
    // Get current month summary
    Route::get('/drawdowns/current-month', [DashboardController::class, 'getCurrentMonthSummary'])
        ->name('api.drawdowns.current-month');
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard.index');
