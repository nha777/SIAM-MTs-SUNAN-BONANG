<?php

use Illuminate\Support\Facades\Route;

// DEV Module: Hanya aktif jika environment bukan production atau debug true
if (app()->environment('local') || config('app.debug') === true) {
    Route::middleware('web')->group(function () {
        Route::get('/dev/components', function () {
            return view('dev.components');
        })->name('dev.components');
    });
}
