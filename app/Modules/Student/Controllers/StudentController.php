<?php

namespace App\Modules\Student\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Requests\StoreStudentRequest;
use App\Modules\Student\Requests\UpdateStudentRequest;
use App\Modules\Student\Services\Contracts\StudentServiceInterface;
use App\Modules\Student\Resources\StudentResource;
use App\Modules\Base\Traits\BaseApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use App\Modules\Academic\Models\AcademicClass;
use App\Modules\Student\Models\Guardian;

class StudentController extends Controller
{
    use BaseApiResponse;

    /**
     * Instance of StudentServiceInterface.
     */
    protected StudentServiceInterface $studentService;

    /**
     * StudentController constructor.
     */
    public function __construct(StudentServiceInterface $studentService)
    {
        $this->studentService = $studentService;
    }

    /**
     * Menampilkan daftar siswa.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Student::class);
        
        $search = $request->input('search');
        $status = $request->input('status');
        
        $query = Student::query();
        
        if ($status === 'all') {
            $query->withTrashed();
        } elseif ($status === 'deleted') {
            $query->onlyTrashed();
        }
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }
        
        $students = $query->paginate(10)->withQueryString();

        if ($request->wantsJson()) {
            return $this->successResponse(StudentResource::collection($students), 'Students retrieved successfully');
        }

        return view('students.index', compact('students'));
    }

    /**
     * Tampilkan form pembuatan siswa baru.
     */
    public function create()
    {
        Gate::authorize('create', Student::class);
        $guardians = Guardian::orderBy('guardian_name')->get();
        $classes = AcademicClass::orderBy('grade')->orderBy('name')->get();
        return view('students.create', compact('guardians', 'classes'));
    }

    /**
     * Menyimpan siswa baru.
     */
    public function store(StoreStudentRequest $request)
    {
        Gate::authorize('create', Student::class);
        $student = $this->studentService->store($request->validated());

        if ($request->wantsJson()) {
            return $this->successResponse(new StudentResource($student), 'Student created successfully', 201);
        }

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail siswa tertentu.
     */
    public function show(Request $request, $id)
    {
        $student = $this->studentService->getById($id, ['*'], ['guardian', 'academicClass']);
        
        if (!$student) {
            if ($request->wantsJson()) {
                return $this->errorResponse('Student not found', 404);
            }
            return redirect()->route('students.index')->with('error', 'Siswa tidak ditemukan.');
        }
        
        Gate::authorize('view', $student);
        
        if ($request->wantsJson()) {
            return $this->successResponse(new StudentResource($student), 'Student retrieved successfully');
        }

        return view('students.show', compact('student'));
    }

    /**
     * Tampilkan form edit siswa.
     */
    public function edit($id)
    {
        $student = $this->studentService->getById($id);
        
        if (!$student) {
            return redirect()->route('students.index')->with('error', 'Siswa tidak ditemukan.');
        }
        
        Gate::authorize('update', $student);
        $guardians = Guardian::orderBy('guardian_name')->get();
        $classes = AcademicClass::orderBy('grade')->orderBy('name')->get();

        return view('students.edit', compact('student', 'guardians', 'classes'));
    }

    /**
     * Memperbarui data siswa tertentu.
     */
    public function update(UpdateStudentRequest $request, $id)
    {
        $student = $this->studentService->getById($id);
        
        if (!$student) {
            if ($request->wantsJson()) {
                return $this->errorResponse('Student not found', 404);
            }
            return redirect()->route('students.index')->with('error', 'Siswa tidak ditemukan.');
        }
        
        Gate::authorize('update', $student);
        $this->studentService->update($id, $request->validated());
        
        $updatedStudent = $this->studentService->getById($id);
        
        if ($request->wantsJson()) {
            return $this->successResponse(new StudentResource($updatedStudent), 'Student updated successfully');
        }

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Menghapus siswa tertentu (soft-delete).
     */
    public function destroy(Request $request, $id)
    {
        $student = $this->studentService->getById($id);
        
        if (!$student) {
            if ($request->wantsJson()) {
                return $this->errorResponse('Student not found', 404);
            }
            return redirect()->route('students.index')->with('error', 'Siswa tidak ditemukan.');
        }
        
        Gate::authorize('delete', $student);
        $this->studentService->remove($id);
        
        if ($request->wantsJson()) {
            return $this->successResponse(null, 'Student deleted successfully');
        }

        return redirect()->route('students.index')->with('success', 'Siswa berhasil dihapus.');
    }

    /**
     * Memulihkan siswa tertentu dari soft-delete.
     */
    public function restore(Request $request, $id)
    {
        $student = Student::withTrashed()->findOrFail($id);
        Gate::authorize('restore', $student);
        
        $this->studentService->restore($id);
        
        if ($request->wantsJson()) {
            return $this->successResponse(null, 'Student restored successfully');
        }

        return redirect()->route('students.index')->with('success', 'Siswa berhasil dipulihkan.');
    }
}
