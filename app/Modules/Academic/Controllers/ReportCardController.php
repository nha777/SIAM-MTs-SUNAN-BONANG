<?php
namespace App\Modules\Academic\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Academic\Requests\ReportCardRequest;
use App\Modules\Academic\Services\ReportCardServiceInterface;
use App\Modules\Academic\Models\AcademicYear;
use App\Modules\Academic\Models\Semester;
use App\Modules\Academic\Models\AcademicClass;
use App\Modules\Student\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Modules\Base\Traits\BaseApiResponse;

class ReportCardController extends Controller {
    use BaseApiResponse;
    protected $service;
    public function __construct(ReportCardServiceInterface $service) { $this->service = $service; }
    
    public function index(Request $request) {
        Gate::authorize('academic.view');
        $reportCards = $this->service->getAll();
        if ($request->wantsJson()) return $this->successResponse($reportCards);
        return view('academic::report-cards.index', compact('reportCards'));
    }

    public function create() {
        Gate::authorize('academic.create');
        $academicYears = AcademicYear::where('is_active', true)->get();
        $semesters = Semester::where('is_active', true)->get();
        $classes = AcademicClass::where('is_active', true)->get();
        $students = Student::orderBy('name')->get();
        return view('academic::report-cards.create', compact('academicYears', 'semesters', 'classes', 'students'));
    }

    public function store(ReportCardRequest $request) {
        Gate::authorize('academic.create');
        $reportCard = $this->service->create($request->validated());
        if ($request->wantsJson()) return $this->successResponse($reportCard, 'Report Card recorded', 201);
        return redirect()->route('report-cards.index')->with('success', 'Rapor berhasil dicatat.');
    }

    public function edit($id) {
        Gate::authorize('academic.update');
        $reportCard = $this->service->getById($id);
        $academicYears = AcademicYear::orderBy('start_year', 'desc')->get();
        $semesters = Semester::orderBy('name')->get();
        $classes = AcademicClass::orderBy('level')->orderBy('name')->get();
        $students = Student::orderBy('name')->get();
        return view('academic::report-cards.edit', compact('reportCard', 'academicYears', 'semesters', 'classes', 'students'));
    }

    public function update(ReportCardRequest $request, $id) {
        Gate::authorize('academic.update');
        $reportCard = $this->service->update($id, $request->validated());
        if ($request->wantsJson()) return $this->successResponse($reportCard, 'Report Card updated');
        return redirect()->route('report-cards.index')->with('success', 'Rapor berhasil diperbarui.');
    }

    public function destroy(Request $request, $id) {
        Gate::authorize('academic.delete');
        $this->service->delete($id);
        if ($request->wantsJson()) return $this->successResponse(null, 'Report Card deleted');
        return redirect()->route('report-cards.index')->with('success', 'Data rapor berhasil dihapus.');
    }
}
