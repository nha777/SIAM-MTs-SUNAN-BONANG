<?php

use App\Modules\Auth\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('prevents user without permission from viewing students', function () {
    $user = User::factory()->create();
    // User has no permissions
    
    $response = $this->actingAs($user)->get(route('students.index'));
    $response->assertStatus(403);
});

it('allows user with permission to view students', function () {
    $user = User::factory()->create();
    $permission = Permission::firstOrCreate(['name' => 'student.view']);
    $user->givePermissionTo($permission);
    
    $response = $this->actingAs($user)->get(route('students.index'));
    $response->assertStatus(200);
});
