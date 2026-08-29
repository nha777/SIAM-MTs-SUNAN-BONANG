<?php

use App\Modules\Auth\Models\User;
use App\Modules\Finance\Models\BillingCategory;
use App\Modules\Academic\Models\AcademicYear;
use App\Modules\Academic\Models\AcademicClass;
use App\Modules\Student\Models\Student;
use App\Modules\Finance\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'Admin Keuangan']);
    $role->syncPermissions(Permission::pluck('name')->toArray());
    $this->user->assignRole($role);
});

it('can render batch invoice create page', function () {
    $this->actingAs($this->user)
        ->get(route('batch-invoices.create'))
        ->assertStatus(200);
});

it('can preview batch invoices with all students target', function () {
    $category = BillingCategory::create([
        'name' => 'SPP',
        'default_amount' => 500000,
        'frequency' => 'Monthly'
    ]);
    
    $year = AcademicYear::create([
        'name' => '2026/2027',
        'start_year' => 2026,
        'end_year' => 2027,
        'is_active' => true
    ]);
    
    // Create students
    Student::create(['name' => 'Siswa A', 'nisn' => '111', 'status' => 'aktif', 'gender' => 'L']);
    Student::create(['name' => 'Siswa B', 'nisn' => '222', 'status' => 'aktif', 'gender' => 'P']);

    $response = $this->actingAs($this->user)
        ->post(route('batch-invoices.preview'), [
            'billing_category_id' => $category->id,
            'academic_year_id' => $year->id,
            'target_type' => 'all',
            'title' => 'SPP Juli 2026',
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'duplicate_action' => 'skip'
        ]);

    $response->assertStatus(200);
    $response->assertSee('Siswa A');
    $response->assertSee('Siswa B');
});

it('can store batch invoices avoiding duplicates', function () {
    $category = BillingCategory::create([
        'name' => 'SPP',
        'default_amount' => 500000,
        'frequency' => 'Monthly'
    ]);
    
    $year = AcademicYear::create([
        'name' => '2026/2027',
        'start_year' => 2026,
        'end_year' => 2027,
        'is_active' => true
    ]);
    
    $student = Student::create(['name' => 'Siswa A', 'nisn' => '111', 'status' => 'aktif', 'gender' => 'L']);
    
    // Existing invoice
    Invoice::create([
        'student_id' => $student->id,
        'academic_year_id' => $year->id,
        'billing_category_id' => $category->id,
        'invoice_number' => 'INV-OLD-01',
        'title' => 'SPP Juli 2026',
        'amount' => 500000,
        'paid_amount' => 0,
        'status' => 'Unpaid',
        'due_date' => now()->addDays(7)->format('Y-m-d'),
    ]);
    
    $previewData = json_encode([
        [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'nisn' => $student->nisn,
            'amount' => 500000,
            'is_duplicate' => true
        ]
    ]);

    // Skip
    $this->actingAs($this->user)
        ->post(route('batch-invoices.store'), [
            'billing_category_id' => $category->id,
            'academic_year_id' => $year->id,
            'title' => 'SPP Juli 2026',
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'duplicate_action' => 'skip',
            'preview_data' => $previewData
        ]);
        
    $this->assertDatabaseCount('invoices', 1);

    // Overwrite
    $this->actingAs($this->user)
        ->post(route('batch-invoices.store'), [
            'billing_category_id' => $category->id,
            'academic_year_id' => $year->id,
            'title' => 'SPP Juli 2026',
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'duplicate_action' => 'overwrite',
            'preview_data' => $previewData
        ]);
        
    $this->assertDatabaseCount('invoices', 1);
    // Should have new invoice number (starts with INV-YYYY-MM)
    $this->assertDatabaseMissing('invoices', ['invoice_number' => 'INV-OLD-01']);
});
