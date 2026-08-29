<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Student\Controllers\StudentController;
use App\Modules\Student\Controllers\GuardianController;

Route::middleware(['web', 'auth'])->group(function () {
    // Student Routes
    Route::get('students', [StudentController::class, 'index'])->name('students.index');
    Route::get('students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('students', [StudentController::class, 'store'])->name('students.store');
    Route::get('students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::get('students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::patch('students/{id}/restore', [StudentController::class, 'restore'])->name('students.restore');

    // Guardian Routes
    Route::get('guardians', [GuardianController::class, 'index'])->name('guardians.index');
    Route::get('guardians/create', [GuardianController::class, 'create'])->name('guardians.create');
    Route::post('guardians', [GuardianController::class, 'store'])->name('guardians.store');
    Route::get('guardians/{guardian}', [GuardianController::class, 'show'])->name('guardians.show');
    Route::get('guardians/{guardian}/edit', [GuardianController::class, 'edit'])->name('guardians.edit');
    Route::put('guardians/{guardian}', [GuardianController::class, 'update'])->name('guardians.update');
    Route::delete('guardians/{guardian}', [GuardianController::class, 'destroy'])->name('guardians.destroy');
    Route::patch('guardians/{id}/restore', [GuardianController::class, 'restore'])->name('guardians.restore');
});
