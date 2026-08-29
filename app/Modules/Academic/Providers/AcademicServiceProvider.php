<?php

namespace App\Modules\Academic\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Academic\Repositories\Contracts\AcademicYearRepositoryInterface;
use App\Modules\Academic\Repositories\AcademicYearRepository;
use App\Modules\Academic\Repositories\Contracts\SemesterRepositoryInterface;
use App\Modules\Academic\Repositories\SemesterRepository;
use App\Modules\Academic\Repositories\Contracts\AcademicClassRepositoryInterface;
use App\Modules\Academic\Repositories\AcademicClassRepository;

class AcademicServiceProvider extends ServiceProvider
{
    /**
     * Daftarkan binding repositori.
     */
    public function register(): void
    {
        $this->app->bind(AcademicYearRepositoryInterface::class, AcademicYearRepository::class);
        $this->app->bind(SemesterRepositoryInterface::class, SemesterRepository::class);
        $this->app->bind(AcademicClassRepositoryInterface::class, AcademicClassRepository::class);

        // Bind Services
        $this->app->bind(
            \App\Modules\Academic\Services\Contracts\AcademicYearServiceInterface::class,
            \App\Modules\Academic\Services\AcademicYearService::class
        );
        $this->app->bind(
            \App\Modules\Academic\Services\Contracts\SemesterServiceInterface::class,
            \App\Modules\Academic\Services\SemesterService::class
        );
        $this->app->bind(
            \App\Modules\Academic\Services\Contracts\AcademicClassServiceInterface::class,
            \App\Modules\Academic\Services\AcademicClassService::class
        );
        $this->app->bind(
            \App\Modules\Academic\Repositories\Contracts\SubjectRepositoryInterface::class,
            \App\Modules\Academic\Repositories\SubjectRepository::class
        );
        $this->app->bind(
            \App\Modules\Academic\Services\Contracts\SubjectServiceInterface::class,
            \App\Modules\Academic\Services\SubjectService::class
        );
        $this->app->bind(
            \App\Modules\Academic\Repositories\Contracts\EnrollmentRepositoryInterface::class,
            \App\Modules\Academic\Repositories\EnrollmentRepository::class
        );
        $this->app->bind(
            \App\Modules\Academic\Services\Contracts\EnrollmentServiceInterface::class,
            \App\Modules\Academic\Services\EnrollmentService::class
        );
    }

    /**
     * Boot service.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::policy(
            \App\Modules\Academic\Models\AcademicYear::class,
            \App\Modules\Academic\Policies\AcademicYearPolicy::class
        );
        \Illuminate\Support\Facades\Gate::policy(
            \App\Modules\Academic\Models\Semester::class,
            \App\Modules\Academic\Policies\SemesterPolicy::class
        );
        \Illuminate\Support\Facades\Gate::policy(
            \App\Modules\Academic\Models\AcademicClass::class,
            \App\Modules\Academic\Policies\AcademicClassPolicy::class
        );
    }
}
