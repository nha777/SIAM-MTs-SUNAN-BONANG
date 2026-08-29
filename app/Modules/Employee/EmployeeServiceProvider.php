<?php

namespace App\Modules\Employee;

use Illuminate\Support\ServiceProvider;

class EmployeeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            \App\Modules\Employee\Repositories\EmployeeRepositoryInterface::class,
            \App\Modules\Employee\Repositories\EmployeeRepository::class
        );
        $this->app->bind(
            \App\Modules\Employee\Services\EmployeeServiceInterface::class,
            \App\Modules\Employee\Services\EmployeeService::class
        );
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/Resources/views', 'employee');
    }
}
