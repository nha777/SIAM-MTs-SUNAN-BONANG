<?php
namespace App\Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Modules\Student\Models\Student;
use App\Modules\Academic\Models\AcademicYear;

class Invoice extends Model 
{
    use HasUuids;
    
    protected $fillable = ['billing_category_id', 'student_id', 'academic_year_id', 'invoice_number', 'title', 'description', 'amount', 'paid_amount', 'status', 'due_date'];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function student() { return $this->belongsTo(Student::class); }
    public function billingCategory() { return $this->belongsTo(BillingCategory::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function payments() { return $this->hasMany(Payment::class); }
}
