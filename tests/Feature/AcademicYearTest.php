<?php

use App\Modules\Academic\Models\AcademicYear;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = \App\Modules\Auth\Models\User::factory()->create();
    $this->user->givePermissionTo(['academic.view', 'academic.create', 'academic.update', 'academic.delete']);
    $this->actingAs($this->user);
});

it('can list academic years', function () {
    $response = $this->get(route('academic-years.index'));
    $response->assertStatus(200);
});

it('can store a new academic year', function () {
    $data = [
        'name' => '2025/2026',
        'start_year' => '2025',
        'end_year' => '2026',
        'is_active' => true
    ];

    $response = $this->post(route('academic-years.store'), $data);
    $response->assertRedirect(route('academic-years.index'));
    
    $this->assertDatabaseHas('academic_years', [
        'name' => '2025/2026'
    ]);
});
