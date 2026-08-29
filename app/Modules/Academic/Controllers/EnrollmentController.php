<?php
namespace App\Modules\Academic\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Academic\Requests\EnrollmentRequest;
use App\Modules\Academic\Services\EnrollmentServiceInterface;
use App\Modules\Academic\Models\AcademicYear;
use App\Modules\Academic\Models\Semester;
use App\Modules\Academic\Models\AcademicClass;
use App\Modules\Student\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Modules\Base\Traits\BaseApiResponse;

class EnrollmentController extends Controller
{
    use BaseApiResponse;

    protected $service;

    public function __construct(EnrollmentServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        Gate::authorize('academic.view');
        
        $academicYearId = $request->input('academic_year_id');
        $semesterId = $request->input('semester_id');
        $classId = $request->input('academic_class_id');
        
        $enrollments = $this->service->getAllEnrollments($academicYearId, $semesterId, $classId);
        
        $academicYears = AcademicYear::orderBy('start_year', 'desc')->get();
        $semesters = Semester::orderBy('name')->get();
        $classes = AcademicClass::orderBy('level')->orderBy('name')->get();
        
        if ($request->wantsJson()) {
            return $this->successResponse($enrollments, 'Enrollments retrieved successfully.');
        }

        return view('academic::enrollments.index', compact('enrollments', 'academicYears', 'semesters', 'classes'));
    }

    public function create()
    {
        Gate::authorize('academic.create');
        
        $academicYears = AcademicYear::where('is_active', true)->get();
        $semesters = Semester::where('is_active', true)->get();
        $classes = AcademicClass::where('is_active', true)->get();
        $students = Student::orderBy('name')->get(); // Di real app, gunakan select2/ajax
        
        return view('academic::enrollments.create', compact('academicYears', 'semesters', 'classes', 'students'));
    }

    public function store(EnrollmentRequest $request)
    {
        Gate::authorize('academic.create');
        
        $enrollment = $this->service->createEnrollment($request->validated());

        if ($request->wantsJson()) {
            return $this->successResponse($enrollment, 'Rombel berhasil ditambahkan.', 201);
        }

        return redirect()->route('enrollments.index')->with('success', 'Siswa berhasil dimasukkan ke rombel.');
    }

    public function edit($id)
    {
        Gate::authorize('academic.update');
        
        $enrollment = $this->service->getEnrollmentById($id);
        $academicYears = AcademicYear::orderBy('start_year', 'desc')->get();
        $semesters = Semester::orderBy('name')->get();
        $classes = AcademicClass::orderBy('level')->orderBy('name')->get();
        $students = Student::orderBy('name')->get();
        
        return view('academic::enrollments.edit', compact('enrollment', 'academicYears', 'semesters', 'classes', 'students'));
    }

    public function update(EnrollmentRequest $request, $id)
    {
        Gate::authorize('academic.update');
        
        $enrollment = $this->service->updateEnrollment($id, $request->validated());

        if ($request->wantsJson()) {
            return $this->successResponse($enrollment, 'Rombel berhasil diperbarui.');
        }

        return redirect()->route('enrollments.index')->with('success', 'Rombel berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        Gate::authorize('academic.delete');
        
        $this->service->deleteEnrollment($id);

        if ($request->wantsJson()) {
            return $this->successResponse(null, 'Rombel berhasil dihapus.');
        }

        return redirect()->route('enrollments.index')->with('success', 'Data rombel berhasil dihapus.');
    }
}
