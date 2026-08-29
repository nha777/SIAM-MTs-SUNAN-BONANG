<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Controllers\AuthController;
use App\Modules\Auth\Controllers\UserController;
use App\Modules\Auth\Controllers\RoleController;
use App\Modules\Auth\Controllers\PermissionController;
use App\Modules\Auth\Controllers\ActivityLogController;
use App\Modules\Auth\Controllers\ProfileController;

Route::middleware('web')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login']);
    });
    
    Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

    Route::middleware('auth')->group(function () {
        // Profile
        Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        // Users
        Route::patch('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::resource('users', UserController::class);

        // Roles
        Route::resource('roles', RoleController::class);

        // Permissions
        Route::resource('permissions', PermissionController::class)->only(['index', 'show']);

        // Activity Logs
        Route::resource('activity-logs', ActivityLogController::class)->only(['index', 'show']);
    });
});
