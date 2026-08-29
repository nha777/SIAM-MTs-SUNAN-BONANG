<?php

use App\Modules\Finance\Console\Commands\SendInvoiceReminders;
use App\Modules\Finance\Models\Invoice;
use App\Modules\Student\Models\Student;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Modules\Finance\Jobs\SendWhatsAppNotificationJob;

it('runs send-reminders command and dispatches jobs for due invoices', function () {
    Queue::fake();

    $student = Student::factory()->create();
    
    // Create a guardian for the student
    $guardianId = DB::table('guardians')->insertGetId([
        'guardian_name' => 'Budi',
        'phone_number' => '081234567890',
        'user_id' => null,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    $student->update(['guardian_id' => $guardianId]);

    // Create an invoice due today
    $invoice = Invoice::create([
        'student_id' => $student->id,
        'academic_year_id' => 1,
        'billing_category_id' => 1,
        'invoice_number' => 'INV-TEST-001',
        'title' => 'Tagihan Test',
        'amount' => 100000,
        'paid_amount' => 0,
        'status' => 'Unpaid',
        'due_date' => Carbon::today()->format('Y-m-d')
    ]);

    // Run command
    $this->artisan('finance:send-reminders')
        ->assertExitCode(0)
        ->expectsOutput('Mencari tagihan yang mendekati jatuh tempo...');

    Queue::assertPushed(SendWhatsAppNotificationJob::class);
});
