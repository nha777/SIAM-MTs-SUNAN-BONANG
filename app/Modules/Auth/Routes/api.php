<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware(['api', 'auth:sanctum'])->prefix('api')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
});
