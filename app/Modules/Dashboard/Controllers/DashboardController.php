<?php
namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Student\Models\Student;
use App\Modules\Employee\Models\Employee;
use App\Modules\Academic\Models\AcademicClass;
use App\Modules\Finance\Models\Invoice;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_students' => Student::count(),
            'total_employees' => Employee::count(),
            'total_classes' => AcademicClass::count(),
            'total_unpaid_invoices' => Invoice::where('status', '!=', 'Paid')->count()
        ];
        
        return view('dashboard::index', compact('stats'));
    }
}
