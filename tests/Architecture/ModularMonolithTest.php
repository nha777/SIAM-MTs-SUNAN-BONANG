<?php

test('modules should not bleed into each other directly')
    ->expect('App\Modules')
    ->toOnlyUse([
        'App\Modules\Base',
        'App\Modules\Auth',
        'Illuminate',
        'Spatie\Permission',
        'Symfony',
    ])
    ->ignoring('App\Modules\Base');

test('controllers should not use models directly')
    ->expect('App\Modules\Auth\Controllers')
    ->not->toUse('App\Modules\Auth\Models');

test('services should not use DB directly')
    ->expect('App\Modules\Auth\Services')
    ->not->toUse('Illuminate\Support\Facades\DB');

test('all services must implement an interface in Contracts', function () {
    $servicesPath = glob(app_path('Modules/*/Services/*.php'));
    
    foreach ($servicesPath as $path) {
        $relative = str_replace(app_path(), 'App', $path);
        $className = str_replace(['/', '.php'], ['\\', ''], $relative);
        
        $reflection = new ReflectionClass($className);
        
        // Skip interfaces and abstract classes
        if ($reflection->isInterface() || $reflection->isAbstract()) {
            continue;
        }
        
        $interfaces = $reflection->getInterfaceNames();
        
        $hasServiceInterface = false;
        foreach ($interfaces as $interface) {
            if (str_contains($interface, 'ServiceInterface')) {
                $hasServiceInterface = true;
                break;
            }
        }
        
        expect($hasServiceInterface)->toBeTrue("Service {$className} does not implement any ServiceInterface contract.");
    }
});

