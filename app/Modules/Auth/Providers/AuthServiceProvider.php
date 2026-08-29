<?php

namespace App\Modules\Auth\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use App\Modules\Auth\Repositories\Contracts\UserRepositoryInterface;
use App\Modules\Auth\Repositories\UserRepository;
use App\Modules\Auth\Services\Contracts\AuthServiceInterface;
use App\Modules\Auth\Services\AuthService;
use App\Modules\Auth\Services\Contracts\UserServiceInterface;
use App\Modules\Auth\Services\UserService;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'auth');
        
        // Map policy for User model
        Gate::policy(\App\Modules\Auth\Models\User::class, \App\Modules\Auth\Policies\UserPolicy::class);

        // Allow checking abilities like 'finance.view' via Spatie permissions
        Gate::before(function ($user, $ability) {
            if (! $user) {
                return null;
            }

            if (! method_exists($user, 'hasPermissionTo')) {
                return null;
            }

            // Only attempt Spatie permission check when a corresponding permission record exists
            try {
                $guard = property_exists($user, 'guard_name') ? $user->guard_name : 'web';

                $permExists = Permission::where('name', $ability)
                    ->where('guard_name', $guard)
                    ->exists();

                if ($permExists && $user->hasPermissionTo($ability)) {
                    return true;
                }
            } catch (\Throwable $e) {
                // swallow errors to avoid breaking authorization when permission system isn't available
            }

            return null;
        });
    }
}
