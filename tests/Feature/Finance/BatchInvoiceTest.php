<?php

use App\Modules\Auth\Models\User;
use App\Modules\Finance\Models\BillingCategory;
use App\Modules\Academic\Models\AcademicYear;
use App\Modules\Student\Models\Student;
use App\Modules\Finance\Models\Invoice;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    Permission::firstOrCreate(['name' => 'finance.create']);
    
    $this->user = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'Admin Keuangan']);
    $role->givePermissionTo('finance.create');
    $this->user->assignRole($role);
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
    
    $student = Student::factory()->create(['status' => 'aktif']);
    
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
    $this->assertDatabaseMissing('invoices', ['invoice_number' => 'INV-OLD-01']);
});
