<?php
namespace App\Modules\Academic\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Academic\Requests\AttendanceRequest;
use App\Modules\Academic\Services\AttendanceServiceInterface;
use App\Modules\Academic\Models\AcademicYear;
use App\Modules\Academic\Models\Semester;
use App\Modules\Academic\Models\AcademicClass;
use App\Modules\Student\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Modules\Base\Traits\BaseApiResponse;

class AttendanceController extends Controller {
    use BaseApiResponse;
    protected $service;
    public function __construct(AttendanceServiceInterface $service) { $this->service = $service; }
    
    public function index(Request $request) {
        Gate::authorize('academic.view');
        $attendances = $this->service->getAll();
        if ($request->wantsJson()) return $this->successResponse($attendances);
        return view('academic::attendances.index', compact('attendances'));
    }

    public function create() {
        Gate::authorize('academic.create');
        $academicYears = AcademicYear::where('is_active', true)->get();
        $semesters = Semester::where('is_active', true)->get();
        $classes = AcademicClass::where('is_active', true)->get();
        $students = Student::orderBy('name')->get();
        return view('academic::attendances.create', compact('academicYears', 'semesters', 'classes', 'students'));
    }

    public function store(AttendanceRequest $request) {
        Gate::authorize('academic.create');
        $attendance = $this->service->create($request->validated());
        if ($request->wantsJson()) return $this->successResponse($attendance, 'Attendance recorded', 201);
        return redirect()->route('attendances.index')->with('success', 'Kehadiran berhasil dicatat.');
    }

    public function edit($id) {
        Gate::authorize('academic.update');
        $attendance = $this->service->getById($id);
        $academicYears = AcademicYear::orderBy('start_year', 'desc')->get();
        $semesters = Semester::orderBy('name')->get();
        $classes = AcademicClass::orderBy('level')->orderBy('name')->get();
        $students = Student::orderBy('name')->get();
        return view('academic::attendances.edit', compact('attendance', 'academicYears', 'semesters', 'classes', 'students'));
    }

    public function update(AttendanceRequest $request, $id) {
        Gate::authorize('academic.update');
        $attendance = $this->service->update($id, $request->validated());
        if ($request->wantsJson()) return $this->successResponse($attendance, 'Attendance updated');
        return redirect()->route('attendances.index')->with('success', 'Kehadiran berhasil diperbarui.');
    }

    public function destroy(Request $request, $id) {
        Gate::authorize('academic.delete');
        $this->service->delete($id);
        if ($request->wantsJson()) return $this->successResponse(null, 'Attendance deleted');
        return redirect()->route('attendances.index')->with('success', 'Data kehadiran berhasil dihapus.');
    }
}
