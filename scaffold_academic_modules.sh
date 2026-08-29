#!/bin/bash

# Define paths
ACADEMIC_DIR="app/Modules/Academic"
FINANCE_DIR="app/Modules/Finance"

# Create directories for Finance
mkdir -p $FINANCE_DIR/{Models,Controllers,Services,Repositories,Requests,Routes,Resources/views/invoices}

# 1. Models
cat << 'PHP' > $ACADEMIC_DIR/Models/Attendance.php
<?php
namespace App\Modules\Academic\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Attendance extends Model {
    use HasUuids;
    protected $fillable = ['student_id', 'academic_year_id', 'semester_id', 'academic_class_id', 'date', 'status', 'notes'];
}
PHP

cat << 'PHP' > $ACADEMIC_DIR/Models/Grade.php
<?php
namespace App\Modules\Academic\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Grade extends Model {
    use HasUuids;
    protected $fillable = ['student_id', 'subject_id', 'academic_year_id', 'semester_id', 'academic_class_id', 'assignment_score', 'mid_exam_score', 'final_exam_score', 'final_score', 'predicate', 'notes'];
}
PHP

cat << 'PHP' > $ACADEMIC_DIR/Models/ReportCard.php
<?php
namespace App\Modules\Academic\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class ReportCard extends Model {
    use HasUuids;
    protected $fillable = ['student_id', 'academic_year_id', 'semester_id', 'academic_class_id', 'total_sick', 'total_permission', 'total_absent', 'homeroom_notes', 'is_locked'];
}
PHP

cat << 'PHP' > $FINANCE_DIR/Models/Invoice.php
<?php
namespace App\Modules\Finance\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Invoice extends Model {
    use HasUuids;
    protected $fillable = ['student_id', 'academic_year_id', 'invoice_number', 'title', 'description', 'amount', 'paid_amount', 'status', 'due_date'];
}
PHP

cat << 'PHP' > $FINANCE_DIR/Models/Payment.php
<?php
namespace App\Modules\Finance\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Payment extends Model {
    use HasUuids;
    protected $fillable = ['invoice_id', 'payment_number', 'amount', 'payment_date', 'payment_method', 'reference_number', 'notes', 'recorded_by'];
}
PHP

# 2. Controllers (Stubs)
cat << 'PHP' > $ACADEMIC_DIR/Controllers/EnrollmentController.php
<?php
namespace App\Modules\Academic\Controllers;
use App\Http\Controllers\Controller;
class EnrollmentController extends Controller {
    public function index() { return view('academic::enrollments.index'); }
}
PHP

cat << 'PHP' > $ACADEMIC_DIR/Controllers/AttendanceController.php
<?php
namespace App\Modules\Academic\Controllers;
use App\Http\Controllers\Controller;
class AttendanceController extends Controller {
    public function index() { return view('academic::attendances.index'); }
}
PHP

cat << 'PHP' > $ACADEMIC_DIR/Controllers/GradeController.php
<?php
namespace App\Modules\Academic\Controllers;
use App\Http\Controllers\Controller;
class GradeController extends Controller {
    public function index() { return view('academic::grades.index'); }
}
PHP

cat << 'PHP' > $ACADEMIC_DIR/Controllers/ReportCardController.php
<?php
namespace App\Modules\Academic\Controllers;
use App\Http\Controllers\Controller;
class ReportCardController extends Controller {
    public function index() { return view('academic::report-cards.index'); }
}
PHP

cat << 'PHP' > $FINANCE_DIR/Controllers/InvoiceController.php
<?php
namespace App\Modules\Finance\Controllers;
use App\Http\Controllers\Controller;
class InvoiceController extends Controller {
    public function index() { return view('finance::invoices.index'); }
}
PHP

# 3. Create basic views
mkdir -p $ACADEMIC_DIR/Resources/views/{enrollments,attendances,grades,report-cards}
for mod in enrollments attendances grades report-cards; do
cat << HTML > $ACADEMIC_DIR/Resources/views/$mod/index.blade.php
@extends('layouts.app')
@section('title', ucfirst('$mod'))
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-surface-900">Module: $mod</h1></div>
<div class="bg-white p-4 shadow sm:rounded-lg">Fitur $mod dalam pengembangan.</div>
@endsection
HTML
done

cat << HTML > $FINANCE_DIR/Resources/views/invoices/index.blade.php
@extends('layouts.app')
@section('title', 'Keuangan')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-surface-900">Keuangan / Tagihan</h1></div>
<div class="bg-white p-4 shadow sm:rounded-lg">Fitur Keuangan dalam pengembangan.</div>
@endsection
HTML

