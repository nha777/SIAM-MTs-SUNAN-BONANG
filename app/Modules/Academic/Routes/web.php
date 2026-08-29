<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Academic\Controllers\AcademicYearController;
use App\Modules\Academic\Controllers\SemesterController;
use App\Modules\Academic\Controllers\AcademicClassController;
use App\Modules\Academic\Controllers\SubjectController;

use App\Modules\Academic\Controllers\EnrollmentController;
use App\Modules\Academic\Controllers\AttendanceController;
use App\Modules\Academic\Controllers\GradeController;
use App\Modules\Academic\Controllers\ReportCardController;



Route::middleware(['web', 'auth'])->group(function () {
    // Academic Year Routes
    Route::get('academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
    Route::get('academic-years/create', [AcademicYearController::class, 'create'])->name('academic-years.create');
    Route::post('academic-years', [AcademicYearController::class, 'store'])->name('academic-years.store');
    Route::get('academic-years/{academic_year}', [AcademicYearController::class, 'show'])->name('academic-years.show');
    Route::get('academic-years/{academic_year}/edit', [AcademicYearController::class, 'edit'])->name('academic-years.edit');
    Route::put('academic-years/{academic_year}', [AcademicYearController::class, 'update'])->name('academic-years.update');
    Route::delete('academic-years/{academic_year}', [AcademicYearController::class, 'destroy'])->name('academic-years.destroy');
    Route::patch('academic-years/{id}/restore', [AcademicYearController::class, 'restore'])->name('academic-years.restore');
    Route::post('academic-years/{id}/activate', [AcademicYearController::class, 'activate'])->name('academic-years.activate');

    // Semester Routes
    Route::get('semesters', [SemesterController::class, 'index'])->name('semesters.index');
    Route::get('semesters/create', [SemesterController::class, 'create'])->name('semesters.create');
    Route::post('semesters', [SemesterController::class, 'store'])->name('semesters.store');
    Route::get('semesters/{semester}', [SemesterController::class, 'show'])->name('semesters.show');
    Route::get('semesters/{semester}/edit', [SemesterController::class, 'edit'])->name('semesters.edit');
    Route::put('semesters/{semester}', [SemesterController::class, 'update'])->name('semesters.update');
    Route::delete('semesters/{semester}', [SemesterController::class, 'destroy'])->name('semesters.destroy');
    Route::patch('semesters/{id}/restore', [SemesterController::class, 'restore'])->name('semesters.restore');
    Route::post('semesters/{id}/activate', [SemesterController::class, 'activate'])->name('semesters.activate');

    // Class Routes
    Route::get('classes', [AcademicClassController::class, 'index'])->name('classes.index');
    Route::get('classes/create', [AcademicClassController::class, 'create'])->name('classes.create');
    Route::post('classes', [AcademicClassController::class, 'store'])->name('classes.store');
    Route::get('classes/{class}', [AcademicClassController::class, 'show'])->name('classes.show');
    Route::get('classes/{class}/edit', [AcademicClassController::class, 'edit'])->name('classes.edit');
    Route::put('classes/{class}', [AcademicClassController::class, 'update'])->name('classes.update');
    Route::delete('classes/{class}', [AcademicClassController::class, 'destroy'])->name('classes.destroy');
    Route::patch('classes/{id}/restore', [AcademicClassController::class, 'restore'])->name('classes.restore');

    // Subjects Routes
    Route::resource('subjects', SubjectController::class);

    Route::resource('enrollments', EnrollmentController::class);
    Route::resource('attendances', AttendanceController::class)->only(['index']);
    Route::resource('grades', GradeController::class)->only(['index']);
    Route::resource('report-cards', ReportCardController::class)->only(['index']);
});
