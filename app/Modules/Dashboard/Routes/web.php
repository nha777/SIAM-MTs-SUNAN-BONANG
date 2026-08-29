<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Dashboard\Controllers\DashboardController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
