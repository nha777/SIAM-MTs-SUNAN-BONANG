<?php

use App\Modules\Auth\Models\User;
use App\Modules\Finance\Models\Invoice;
use App\Modules\Finance\Models\Payment;
use App\Modules\Student\Models\Student;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use App\Modules\Academic\Models\AcademicYear;
use App\Modules\Finance\Models\BillingCategory;

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    Permission::firstOrCreate(['name' => 'finance.create']);
    Permission::firstOrCreate(['name' => 'finance.update']);
    
    $this->user = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'Admin Keuangan']);
    $role->givePermissionTo(['finance.create', 'finance.update']);
    $this->user->assignRole($role);
});

it('cannot verify payment if it exceeds invoice amount', function () {
    $student = Student::factory()->create();
    
    $invoice = Invoice::create([
        'student_id' => $student->id,
        'academic_year_id' => 1,
        'billing_category_id' => 1,
        'invoice_number' => 'INV-TEST-002',
        'title' => 'Tagihan Test',
        'amount' => 100000,
        'paid_amount' => 0,
        'status' => 'Unpaid',
        'due_date' => now()->addDays(7)->format('Y-m-d')
    ]);

    $payment = Payment::create([
        'invoice_id' => $invoice->id,
        'payment_number' => 'PAY-TEST-001',
        'amount' => 150000, // Exceeds invoice amount
        'payment_date' => now()->format('Y-m-d'),
        'payment_method' => 'Tunai',
        'status' => 'Pending',
        'recorded_by' => $this->user->id
    ]);

    $response = $this->actingAs($this->user)
        ->patch(route('verifications.verify', $payment->id), [
            'status' => 'Verified'
        ]);

    $response->assertSessionHas('error');
    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'status' => 'Pending' // Should not be verified
    ]);
});

it('cannot create payment if invoice is already paid', function () {
    $student = Student::factory()->create();
    
    $invoice = Invoice::create([
        'student_id' => $student->id,
        'academic_year_id' => 1,
        'billing_category_id' => 1,
        'invoice_number' => 'INV-TEST-003',
        'title' => 'Tagihan Lunas',
        'amount' => 100000,
        'paid_amount' => 100000,
        'status' => 'Paid',
        'due_date' => now()->addDays(7)->format('Y-m-d')
    ]);

    $response = $this->actingAs($this->user)
        ->post(route('payments.store'), [
            'invoice_id' => $invoice->id,
            'amount' => 50000,
            'payment_date' => now()->format('Y-m-d'),
            'payment_method' => 'Tunai'
        ]);

    $response->assertSessionHas('error');
});

it('forbids parent from paying other student invoice', function () {
    // Setup Parent
    $parent = User::factory()->create();
    $guardianId = DB::table('guardians')->insertGetId([
        'guardian_name' => 'Wali Murid',
        'phone_number' => '081234',
        'user_id' => $parent->id
    ]);
    
    $student = Student::factory()->create(['guardian_id' => $guardianId]);
    $otherStudent = Student::factory()->create();
    
    $invoice = Invoice::create([
        'student_id' => $otherStudent->id,
        'academic_year_id' => 1,
        'billing_category_id' => 1,
        'invoice_number' => 'INV-OTHER-001',
        'title' => 'Tagihan Orang Lain',
        'amount' => 100000,
        'paid_amount' => 0,
        'status' => 'Unpaid',
        'due_date' => now()->addDays(7)->format('Y-m-d')
    ]);

    $response = $this->actingAs($parent)
        ->post(route('portal.payment.submit', $invoice->id), [
            'amount' => 100000,
            'payment_date' => now()->format('Y-m-d'),
            'payment_method' => 'Transfer Bank',
            'reference_number' => 'REF123',
            'proof' => \Illuminate\Http\UploadedFile::fake()->image('proof.jpg')
        ]);

    $response->assertStatus(403);
});
