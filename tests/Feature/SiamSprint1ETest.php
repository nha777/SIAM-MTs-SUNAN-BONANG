<?php

namespace Tests\Feature;

use App\Modules\Auth\Models\User;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\Guardian;
use App\Modules\Academic\Models\AcademicYear;
use App\Modules\Academic\Models\Semester;
use App\Modules\Academic\Models\AcademicClass;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RolePermissionSeeder;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Run seeders to set up roles and permissions
    $this->seed(RolePermissionSeeder::class);
});

test('Guest tidak bisa akses student index', function () {
    $response = $this->getJson('/students');
    $response->assertStatus(401);
});

test('TU bisa membuat siswa', function () {
    $tu = User::create([
        'name' => 'Tata Usaha',
        'email' => 'tu@siam.test',
        'password' => bcrypt('password'),
        'is_active' => true,
    ]);
    $tu->assignRole('Tata Usaha');

    // Create prerequisite guardian, semester & academic class
    $guardian = Guardian::create([
        'guardian_name' => 'Slamet Santoso',
        'guardian_relation' => 'ayah',
        'phone_number' => '081234567890',
        'address' => 'Jl. Merdeka No. 10',
    ]);

    $year = AcademicYear::create(['name' => '2026/2027', 'is_active' => true]);
    $semester = Semester::create(['academic_year_id' => $year->id, 'semester' => 'ganjil', 'is_active' => true]);
    $class = AcademicClass::create(['semester_id' => $semester->id, 'name' => 'Kelas VII-A', 'grade' => 7]);

    $studentData = [
        'nisn' => '1234567890',
        'name' => 'Budi Santoso',
        'gender' => 'L',
        'birth_place' => 'Jakarta',
        'birth_date' => '2010-05-15',
        'status' => 'aktif',
        'class_id' => $class->id,
        'guardian_id' => $guardian->id,
    ];

    $response = $this->actingAs($tu)
        ->postJson('/students', $studentData);

    $response->assertStatus(201);
    $response->assertJsonStructure([
        'success',
        'message',
        'data' => [
            'id',
            'nisn',
            'name',
            'gender',
            'birth_place',
            'birth_date',
            'status',
            'guardian_id',
            'class_id',
        ]
    ]);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.nisn'))->toBe('1234567890');
    expect($response->json('data.name'))->toBe('Budi Santoso');
    $this->assertDatabaseHas('students', ['nisn' => '1234567890']);
});

test('TU bisa mengubah siswa', function () {
    $tu = User::create([
        'name' => 'Tata Usaha',
        'email' => 'tu@siam.test',
        'password' => bcrypt('password'),
        'is_active' => true,
    ]);
    $tu->assignRole('Tata Usaha');

    // Create prerequisite data
    $guardian = Guardian::create([
        'guardian_name' => 'Slamet Santoso',
        'guardian_relation' => 'ayah',
        'phone_number' => '081234567890',
        'address' => 'Jl. Merdeka No. 10',
    ]);

    $year = AcademicYear::create(['name' => '2026/2027', 'is_active' => true]);
    $semester = Semester::create(['academic_year_id' => $year->id, 'semester' => 'ganjil', 'is_active' => true]);
    $class = AcademicClass::create(['semester_id' => $semester->id, 'name' => 'Kelas VII-A', 'grade' => 7]);

    $student = Student::create([
        'nisn' => '1234567890',
        'name' => 'Budi Santoso',
        'gender' => 'L',
        'birth_place' => 'Jakarta',
        'birth_date' => '2010-05-15',
        'status' => 'aktif',
        'class_id' => $class->id,
        'guardian_id' => $guardian->id,
    ]);

    $updateData = [
        'nisn' => '1234567890',
        'name' => 'Budi Santoso Wibowo',
        'gender' => 'L',
        'birth_place' => 'Bandung',
        'birth_date' => '2010-05-15',
        'status' => 'aktif',
        'class_id' => $class->id,
        'guardian_id' => $guardian->id,
    ];

    $response = $this->actingAs($tu)
        ->putJson("/students/{$student->id}", $updateData);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'message',
        'data' => [
            'id',
            'nisn',
            'name',
            'gender',
            'birth_place',
            'birth_date',
            'status',
            'guardian_id',
            'class_id',
        ]
    ]);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.name'))->toBe('Budi Santoso Wibowo');
    $this->assertDatabaseHas('students', [
        'id' => $student->id,
        'name' => 'Budi Santoso Wibowo',
        'birth_place' => 'Bandung'
    ]);
});

test('Wali Kelas tidak bisa menghapus siswa', function () {
    $waliKelas = User::create([
        'name' => 'Wali Kelas',
        'email' => 'walikelas@siam.test',
        'password' => bcrypt('password'),
        'is_active' => true,
    ]);
    $waliKelas->assignRole('Wali Kelas');

    $guardian = Guardian::create([
        'guardian_name' => 'Slamet Santoso',
        'guardian_relation' => 'ayah',
        'phone_number' => '081234567890',
        'address' => 'Jl. Merdeka No. 10',
    ]);

    $student = Student::create([
        'nisn' => '1234567890',
        'name' => 'Budi Santoso',
        'gender' => 'L',
        'birth_place' => 'Jakarta',
        'birth_date' => '2010-05-15',
        'status' => 'aktif',
        'guardian_id' => $guardian->id,
    ]);

    $response = $this->actingAs($waliKelas)
        ->deleteJson("/students/{$student->id}");

    $response->assertStatus(403);
});

test('Bendahara tidak bisa membuat tahun ajaran', function () {
    $bendahara = User::create([
        'name' => 'Bendahara',
        'email' => 'bendahara@siam.test',
        'password' => bcrypt('password'),
        'is_active' => true,
    ]);
    $bendahara->assignRole('Bendahara');

    $response = $this->actingAs($bendahara)
        ->postJson('/academic-years', [
            'name' => '2027/2028',
            'is_active' => false,
        ]);

    $response->assertStatus(403);
});

test('Super Admin bisa mengaktifkan semester', function () {
    $admin = User::create([
        'name' => 'Super Admin',
        'email' => 'admin@siam.test',
        'password' => bcrypt('password'),
        'is_active' => true,
    ]);
    $admin->assignRole('Super Admin');

    $year = AcademicYear::create(['name' => '2026/2027', 'is_active' => true]);
    $semester = Semester::create(['academic_year_id' => $year->id, 'semester' => 'ganjil', 'is_active' => false]);

    $response = $this->actingAs($admin)
        ->postJson("/semesters/{$semester->id}/activate");

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'message',
    ]);
    expect($response->json('success'))->toBeTrue();
    expect($semester->fresh()->is_active)->toBeTrue();
});

test('Super Admin bisa mengaktifkan tahun ajaran', function () {
    $admin = User::create([
        'name' => 'Super Admin',
        'email' => 'admin@siam.test',
        'password' => bcrypt('password'),
        'is_active' => true,
    ]);
    $admin->assignRole('Super Admin');

    $year = AcademicYear::create(['name' => '2026/2027', 'is_active' => false]);

    $response = $this->actingAs($admin)
        ->postJson("/academic-years/{$year->id}/activate");

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'message',
    ]);
    expect($response->json('success'))->toBeTrue();
    expect($year->fresh()->is_active)->toBeTrue();
});

test('Orang Tua tidak bisa melihat seluruh siswa', function () {
    $orangTua = User::create([
        'name' => 'Orang Tua',
        'email' => 'ortu@siam.test',
        'password' => bcrypt('password'),
        'is_active' => true,
    ]);
    $orangTua->assignRole('Orang Tua');

    $response = $this->actingAs($orangTua)
        ->getJson('/students');

    $response->assertStatus(403);
});
