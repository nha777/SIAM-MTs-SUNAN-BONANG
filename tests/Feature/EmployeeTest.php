<?php

use App\Modules\Employee\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = \App\Modules\Auth\Models\User::factory()->create();
    $this->user->givePermissionTo(['employee.view', 'employee.create', 'employee.update', 'employee.delete']);
    $this->actingAs($this->user);
});

it('can list employees', function () {
    $response = $this->get(route('employees.index'));
    $response->assertStatus(200);
});

it('can show create employee form', function () {
    $response = $this->get(route('employees.create'));
    $response->assertStatus(200);
});

it('can store a new employee', function () {
    $data = [
        'name' => 'John Doe Teacher',
        'gender' => 'L',
        'position' => 'Guru',
    ];

    $response = $this->post(route('employees.store'), $data);
    $response->assertRedirect(route('employees.index'));
    
    $this->assertDatabaseHas('employees', [
        'name' => 'John Doe Teacher',
        'position' => 'Guru'
    ]);
});

it('can update an employee', function () {
    $employee = Employee::create([
        'name' => 'Old Name',
        'gender' => 'P',
        'position' => 'Staff'
    ]);

    $data = [
        'name' => 'New Name Updated',
        'gender' => 'P',
        'position' => 'Staff'
    ];

    $response = $this->put(route('employees.update', $employee->id), $data);
    $response->assertRedirect(route('employees.index'));
    
    $this->assertDatabaseHas('employees', [
        'id' => $employee->id,
        'name' => 'New Name Updated'
    ]);
});

it('can delete an employee', function () {
    $employee = Employee::create([
        'name' => 'To Be Deleted',
        'gender' => 'L',
        'position' => 'Guru'
    ]);

    $response = $this->delete(route('employees.destroy', $employee->id));
    $response->assertRedirect(route('employees.index'));
    
    $this->assertSoftDeleted('employees', [
        'id' => $employee->id
    ]);
});
