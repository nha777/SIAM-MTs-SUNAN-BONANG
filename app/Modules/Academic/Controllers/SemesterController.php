<?php

namespace App\Modules\Academic\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Academic\Models\Semester;
use App\Modules\Academic\Requests\StoreSemesterRequest;
use App\Modules\Academic\Requests\UpdateSemesterRequest;
use App\Modules\Academic\Services\Contracts\SemesterServiceInterface;
use App\Modules\Academic\Resources\SemesterResource;
use App\Modules\Base\Traits\BaseApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SemesterController extends Controller
{
    use BaseApiResponse;

    /**
     * Instance of SemesterServiceInterface.
     */
    protected SemesterServiceInterface $semesterService;

    /**
     * SemesterController constructor.
     */
    public function __construct(SemesterServiceInterface $semesterService)
    {
        $this->semesterService = $semesterService;
    }

    /**
     * Menampilkan daftar semester.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Semester::class);

        $status = $request->input('status');
        $search = $request->input('search');
        
        $query = Semester::query()->with('academicYear');
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('semester', 'like', "%{$search}%")
                  ->orWhereHas('academicYear', function($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($status === 'all') {
            $query->withTrashed();
        } elseif ($status === 'deleted') {
            $query->onlyTrashed();
        }

        $semesters = $query->paginate(10)->withQueryString();

        if ($request->wantsJson()) {
            return $this->successResponse(SemesterResource::collection($semesters), 'Semesters retrieved successfully');
        }

        return view('semesters.index', compact('semesters'));
    }

    /**
     * Menampilkan form pembuatan semester baru.
     */
    public function create()
    {
        Gate::authorize('create', Semester::class);
        $academicYears = \App\Modules\Academic\Models\AcademicYear::orderBy('name', 'desc')->get();
        return view('semesters.create', compact('academicYears'));
    }

    /**
     * Menyimpan semester baru.
     */
    public function store(StoreSemesterRequest $request)
    {
        Gate::authorize('create', Semester::class);

        $semester = $this->semesterService->store($request->validated());
        
        if ($request->wantsJson()) {
            return $this->successResponse(new SemesterResource($semester), 'Semester created successfully', 201);
        }

        return redirect()->route('semesters.index')->with('success', 'Data semester berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail semester tertentu.
     */
    public function show(Request $request, $id)
    {
        $semester = $this->semesterService->getById($id);
        
        if (!$semester) {
            if ($request->wantsJson()) {
                return $this->errorResponse('Semester not found', 404);
            }
            return redirect()->route('semesters.index')->with('error', 'Semester tidak ditemukan.');
        }

        Gate::authorize('view', $semester);

        if ($request->wantsJson()) {
            return $this->successResponse(new SemesterResource($semester), 'Semester retrieved successfully');
        }

        return view('semesters.show', compact('semester'));
    }

    /**
     * Menampilkan form edit semester.
     */
    public function edit($id)
    {
        $semester = $this->semesterService->getById($id);
        
        if (!$semester) {
            return redirect()->route('semesters.index')->with('error', 'Semester tidak ditemukan.');
        }
        
        Gate::authorize('update', $semester);
        
        $academicYears = \App\Modules\Academic\Models\AcademicYear::orderBy('name', 'desc')->get();
        return view('semesters.edit', compact('semester', 'academicYears'));
    }

    /**
     * Memperbarui data semester tertentu.
     */
    public function update(UpdateSemesterRequest $request, $id)
    {
        $semester = $this->semesterService->getById($id);
        
        if (!$semester) {
            if ($request->wantsJson()) {
                return $this->errorResponse('Semester not found', 404);
            }
            return redirect()->route('semesters.index')->with('error', 'Semester tidak ditemukan.');
        }

        Gate::authorize('update', $semester);

        $this->semesterService->update($id, $request->validated());

        $updatedSemester = $this->semesterService->getById($id);
        
        if ($request->wantsJson()) {
            return $this->successResponse(new SemesterResource($updatedSemester), 'Semester updated successfully');
        }

        return redirect()->route('semesters.index')->with('success', 'Data semester berhasil diperbarui.');
    }

    /**
     * Mengaktifkan semester tertentu dan menonaktifkan yang lainnya.
     */
    public function activate(Request $request, $id)
    {
        $semester = $this->semesterService->getById($id);
        
        if (!$semester) {
            if ($request->wantsJson()) {
                return $this->errorResponse('Semester not found', 404);
            }
            return redirect()->route('semesters.index')->with('error', 'Semester tidak ditemukan.');
        }

        Gate::authorize('activate', $semester);

        $this->semesterService->activate($id);

        if ($request->wantsJson()) {
            return $this->successResponse(null, 'Semester activated successfully');
        }

        return redirect()->route('semesters.index')->with('success', 'Semester berhasil diaktifkan.');
    }

    /**
     * Menghapus semester (Soft Delete).
     */
    public function destroy(Request $request, $id)
    {
        $semester = $this->semesterService->getById($id);
        
        if (!$semester) {
            if ($request->wantsJson()) {
                return $this->errorResponse('Semester not found', 404);
            }
            return redirect()->route('semesters.index')->with('error', 'Semester tidak ditemukan.');
        }
        
        Gate::authorize('delete', $semester);
        
        // Prevent deleting active semester
        if ($semester->is_active) {
            if ($request->wantsJson()) {
                return $this->errorResponse('Cannot delete an active semester. Activate another one first.', 400);
            }
            return redirect()->route('semesters.index')->with('error', 'Semester aktif tidak dapat dihapus. Aktifkan semester lain terlebih dahulu.');
        }
        
        $this->semesterService->remove($id);
        
        if ($request->wantsJson()) {
            return $this->successResponse(null, 'Semester deleted successfully');
        }

        return redirect()->route('semesters.index')->with('success', 'Semester berhasil dihapus.');
    }

    /**
     * Memulihkan semester dari soft-delete.
     */
    public function restore(Request $request, $id)
    {
        $semester = Semester::withTrashed()->findOrFail($id);
        Gate::authorize('restore', $semester);
        
        $this->semesterService->restore($id);
        
        if ($request->wantsJson()) {
            return $this->successResponse(null, 'Semester restored successfully');
        }

        return redirect()->route('semesters.index')->with('success', 'Semester berhasil dipulihkan.');
    }
}
