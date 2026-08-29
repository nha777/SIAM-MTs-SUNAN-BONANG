<?php

namespace App\Modules\Finance\Providers;

use Illuminate\Support\ServiceProvider;

class FinanceServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            \App\Modules\Finance\Repositories\BillingCategoryRepositoryInterface::class,
            \App\Modules\Finance\Repositories\BillingCategoryRepository::class
        );
        $this->app->bind(
            \App\Modules\Finance\Services\BillingCategoryServiceInterface::class,
            \App\Modules\Finance\Services\BillingCategoryService::class
        );
    
        $this->app->bind(
            \App\Modules\Finance\Repositories\InvoiceRepositoryInterface::class,
            \App\Modules\Finance\Repositories\InvoiceRepository::class
        );
        $this->app->bind(
            \App\Modules\Finance\Services\InvoiceServiceInterface::class,
            \App\Modules\Finance\Services\InvoiceService::class
        );
        $this->app->bind(
            \App\Modules\Finance\Repositories\PaymentRepositoryInterface::class,
            \App\Modules\Finance\Repositories\PaymentRepository::class
        );
        $this->app->bind(
            \App\Modules\Finance\Services\PaymentServiceInterface::class,
            \App\Modules\Finance\Services\PaymentService::class
        );
        $this->app->bind(
            \App\Modules\Finance\Services\Notifications\NotificationServiceInterface::class,
            \App\Modules\Finance\Services\Notifications\WhatsAppNotificationService::class
        );
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Modules\Finance\Console\Commands\SendInvoiceReminders::class,
            ]);
        }
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'finance');
    }
}
