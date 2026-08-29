<?php

namespace App\Modules\Base\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Contracts\Http\Kernel;
use App\Modules\Base\Events\ModelMutatedEvent;
use App\Modules\Base\Listeners\AuditListener;
use App\Http\Middleware\RequestCorrelationMiddleware;

class BaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register Policy
        \Illuminate\Support\Facades\Gate::policy(\App\Modules\Base\Models\AuditLog::class, \App\Modules\Base\Policies\AuditLogPolicy::class);

        // Register Event Listener
        Event::listen(
            ModelMutatedEvent::class,
            AuditListener::class
        );

        // Register global middleware if HTTP Kernel is bound
        if ($this->app->bound(Kernel::class)) {
            $kernel = $this->app->make(Kernel::class);
            $kernel->pushMiddleware(RequestCorrelationMiddleware::class);
        }
    }
}
