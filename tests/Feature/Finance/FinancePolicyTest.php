<?php

use App\Modules\Auth\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Modules\Finance\Models\Invoice;

beforeEach(function () {
    // Setup roles and permissions
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    
    Permission::firstOrCreate(['name' => 'finance.view']);
    Permission::firstOrCreate(['name' => 'finance.create']);
    Permission::firstOrCreate(['name' => 'finance.update']);
    Permission::firstOrCreate(['name' => 'finance.delete']);
    
    $this->adminKeuangan = User::factory()->create();
    $roleAdmin = Role::firstOrCreate(['name' => 'Admin Keuangan']);
    $roleAdmin->givePermissionTo(['finance.view', 'finance.create', 'finance.update', 'finance.delete']);
    $this->adminKeuangan->assignRole('Admin Keuangan');
    
    $this->guru = User::factory()->create();
    $roleGuru = Role::firstOrCreate(['name' => 'Guru']);
    $this->guru->assignRole('Guru');
});

it('allows Admin Keuangan to view finance dashboard', function () {
    $this->actingAs($this->adminKeuangan)
        ->get(route('financial-dashboard.index'))
        ->assertStatus(200);
});

it('forbids Guru from viewing finance dashboard', function () {
    $this->actingAs($this->guru)
        ->get(route('financial-dashboard.index'))
        ->assertStatus(403);
});

it('forbids Guru from creating batch invoices', function () {
    $this->actingAs($this->guru)
        ->get(route('batch-invoices.create'))
        ->assertStatus(403);
});
