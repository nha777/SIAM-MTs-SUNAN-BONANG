<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Daftarkan modul yang ditemukan di direktori app/Modules.
     */
    public function register(): void
    {
        $modulesPath = app_path('Modules');
        if (!File::exists($modulesPath)) {
            return;
        }

        $modules = File::directories($modulesPath);
        foreach ($modules as $module) {
            $moduleName = basename($module);
            
            // Registrasi ServiceProvider internal jika ada di setiap modul
            // Contoh: App\Modules\Auth\Providers\AuthServiceProvider
            $customProvider = "App\\Modules\\{$moduleName}\\Providers\\{$moduleName}ServiceProvider";
            if (class_exists($customProvider)) {
                $this->app->register($customProvider);
            }
        }
    }

    /**
     * Muat aset, route, migrasi, dan view untuk setiap modul.
     */
    public function boot(): void
    {
        $modulesPath = app_path('Modules');
        if (!File::exists($modulesPath)) {
            return;
        }

        $modules = File::directories($modulesPath);
        foreach ($modules as $module) {
            $moduleName = basename($module);
            
            $moduleLower = strtolower($moduleName);
            // 1. Muat Routes (Web & API)
            if (File::exists($module . '/Routes/web.php')) {
                $this->loadRoutesFrom($module . '/Routes/web.php');
            }
            if (File::exists($module . '/Routes/api.php')) {
                $this->loadRoutesFrom($module . '/Routes/api.php');
            }
            // 2. Muat Migrasi internal modul (jika ada)
            if (File::exists($module . '/Database/Migrations')) {
                $this->loadMigrationsFrom($module . '/Database/Migrations');
            }
            // 3. Muat Views (Namespace: 'auth::view-name')
            if (File::exists($module . '/Views')) {
                $this->loadViewsFrom($module . '/Views', $moduleLower);
            }
            // 4. Muat Translations (Namespace: __('auth::messages.welcome'))
            if (File::exists($module . '/Translations')) {
                $this->loadTranslationsFrom($module . '/Translations', $moduleLower);
            }
        }
    }
}
