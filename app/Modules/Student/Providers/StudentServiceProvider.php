<?php

namespace App\Modules\Student\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Student\Repositories\Contracts\GuardianRepositoryInterface;
use App\Modules\Student\Repositories\GuardianRepository;
use App\Modules\Student\Repositories\Contracts\StudentRepositoryInterface;
use App\Modules\Student\Repositories\StudentRepository;

class StudentServiceProvider extends ServiceProvider
{
    /**
     * Daftarkan binding repositori.
     */
    public function register(): void
    {
        $this->app->bind(GuardianRepositoryInterface::class, GuardianRepository::class);
        $this->app->bind(StudentRepositoryInterface::class, StudentRepository::class);

        // Bind Services
        $this->app->bind(
            \App\Modules\Student\Services\Contracts\GuardianServiceInterface::class,
            \App\Modules\Student\Services\GuardianService::class
        );
        $this->app->bind(
            \App\Modules\Student\Services\Contracts\StudentServiceInterface::class,
            \App\Modules\Student\Services\StudentService::class
        );
    }

    /**
     * Boot service.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::policy(
            \App\Modules\Student\Models\Student::class,
            \App\Modules\Student\Policies\StudentPolicy::class
        );
        \Illuminate\Support\Facades\Gate::policy(
            \App\Modules\Student\Models\Guardian::class,
            \App\Modules\Student\Policies\GuardianPolicy::class
        );
    }
}
