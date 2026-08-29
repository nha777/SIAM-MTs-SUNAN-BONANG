<?php

use App\Modules\Academic\Models\Enrollment;
use App\Modules\Student\Models\Student;
use App\Modules\Academic\Models\AcademicYear;
use App\Modules\Academic\Models\Semester;
use App\Modules\Academic\Models\AcademicClass;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = \App\Modules\Auth\Models\User::factory()->create();
    $this->user->givePermissionTo(['academic.view', 'academic.create', 'academic.update', 'academic.delete']);
    $this->actingAs($this->user);
});

it('can list enrollments', function () {
    $response = $this->get(route('enrollments.index'));
    $response->assertStatus(200);
});

it('can store a new enrollment', function () {
    $student = Student::create(['name' => 'Student 1', 'nisn' => '12345', 'gender' => 'L']);
    $ay = AcademicYear::create(['name' => '2023/2024', 'start_year' => '2023', 'end_year' => '2024']);
    $semester = Semester::create(['name' => 'Ganjil', 'academic_year_id' => $ay->id]);
    $class = AcademicClass::create(['name' => 'X-A', 'level' => '10']);

    $data = [
        'student_id' => $student->id,
        'academic_year_id' => $ay->id,
        'semester_id' => $semester->id,
        'academic_class_id' => $class->id,
        'enrollment_date' => '2023-07-15',
        'status' => 'Aktif'
    ];

    $response = $this->post(route('enrollments.store'), $data);
    $response->assertRedirect(route('enrollments.index'));
    
    $this->assertDatabaseHas('enrollments', [
        'student_id' => $student->id,
        'academic_class_id' => $class->id
    ]);
});
