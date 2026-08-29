<?php
namespace App\Modules\Academic\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Academic\Requests\GradeRequest;
use App\Modules\Academic\Services\GradeServiceInterface;
use App\Modules\Academic\Models\AcademicYear;
use App\Modules\Academic\Models\Semester;
use App\Modules\Academic\Models\AcademicClass;
use App\Modules\Academic\Models\Subject;
use App\Modules\Student\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Modules\Base\Traits\BaseApiResponse;

class GradeController extends Controller {
    use BaseApiResponse;
    protected $service;
    public function __construct(GradeServiceInterface $service) { $this->service = $service; }
    
    public function index(Request $request) {
        Gate::authorize('academic.view');
        $grades = $this->service->getAll();
        if ($request->wantsJson()) return $this->successResponse($grades);
        return view('academic::grades.index', compact('grades'));
    }

    public function create() {
        Gate::authorize('academic.create');
        $academicYears = AcademicYear::where('is_active', true)->get();
        $semesters = Semester::where('is_active', true)->get();
        $classes = AcademicClass::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $students = Student::orderBy('name')->get();
        return view('academic::grades.create', compact('academicYears', 'semesters', 'classes', 'subjects', 'students'));
    }

    public function store(GradeRequest $request) {
        Gate::authorize('academic.create');
        $grade = $this->service->create($request->validated());
        if ($request->wantsJson()) return $this->successResponse($grade, 'Grade recorded', 201);
        return redirect()->route('grades.index')->with('success', 'Nilai berhasil dicatat.');
    }

    public function edit($id) {
        Gate::authorize('academic.update');
        $grade = $this->service->getById($id);
        $academicYears = AcademicYear::orderBy('start_year', 'desc')->get();
        $semesters = Semester::orderBy('name')->get();
        $classes = AcademicClass::orderBy('level')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $students = Student::orderBy('name')->get();
        return view('academic::grades.edit', compact('grade', 'academicYears', 'semesters', 'classes', 'subjects', 'students'));
    }

    public function update(GradeRequest $request, $id) {
        Gate::authorize('academic.update');
        $grade = $this->service->update($id, $request->validated());
        if ($request->wantsJson()) return $this->successResponse($grade, 'Grade updated');
        return redirect()->route('grades.index')->with('success', 'Nilai berhasil diperbarui.');
    }

    public function destroy(Request $request, $id) {
        Gate::authorize('academic.delete');
        $this->service->delete($id);
        if ($request->wantsJson()) return $this->successResponse(null, 'Grade deleted');
        return redirect()->route('grades.index')->with('success', 'Data nilai berhasil dihapus.');
    }
}
