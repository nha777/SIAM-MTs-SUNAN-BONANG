<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Employee\Controllers\EmployeeController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::resource('employees', EmployeeController::class);
});
