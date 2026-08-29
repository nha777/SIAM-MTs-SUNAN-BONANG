<?php

use App\Modules\Academic\Models\Semester;
use App\Modules\Academic\Models\AcademicYear;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = \App\Modules\Auth\Models\User::factory()->create();
    $this->user->givePermissionTo(['academic.view', 'academic.create', 'academic.update', 'academic.delete']);
    $this->actingAs($this->user);
});

it('can list semesters', function () {
    $response = $this->get(route('semesters.index'));
    $response->assertStatus(200);
});

it('can store a new semester', function () {
    $ay = AcademicYear::create(['name' => '2025/2026', 'start_year' => '2025', 'end_year' => '2026']);
    
    $data = [
        'academic_year_id' => $ay->id,
        'name' => 'Genap',
        'is_active' => true
    ];

    $response = $this->post(route('semesters.store'), $data);
    $response->assertRedirect(route('semesters.index'));
    
    $this->assertDatabaseHas('semesters', [
        'name' => 'Genap',
        'academic_year_id' => $ay->id
    ]);
});
