<?php

namespace App\Modules\Academic\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Academic\Models\AcademicClass;
use App\Modules\Academic\Requests\StoreAcademicClassRequest;
use App\Modules\Academic\Requests\UpdateAcademicClassRequest;
use App\Modules\Academic\Services\Contracts\AcademicClassServiceInterface;
use App\Modules\Academic\Resources\AcademicClassResource;
use App\Modules\Base\Traits\BaseApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AcademicClassController extends Controller
{
    use BaseApiResponse;

    protected AcademicClassServiceInterface $academicClassService;

    public function __construct(AcademicClassServiceInterface $academicClassService)
    {
        $this->academicClassService = $academicClassService;
    }

    public function index(Request $request)
    {
        Gate::authorize('viewAny', AcademicClass::class);
        
        $status = $request->input('status');
        $search = $request->input('search');
        $grade = $request->input('grade');

        $query = AcademicClass::with('academicYear')->orderBy('display_order');
        
        if ($status === 'all') {
            $query->withTrashed();
        } elseif ($status === 'deleted') {
            $query->onlyTrashed();
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($grade) {
            $query->where('grade', $grade);
        }

        $classes = $query->paginate(10)->withQueryString();

        if ($request->wantsJson()) {
            return $this->successResponse(AcademicClassResource::collection($classes), 'Classes retrieved successfully');
        }

        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        Gate::authorize('create', AcademicClass::class);
        $academicYears = \App\Modules\Academic\Models\AcademicYear::orderBy('start_year', 'desc')->get();
        return view('classes.create', compact('academicYears'));
    }

    public function store(StoreAcademicClassRequest $request)
    {
        Gate::authorize('create', AcademicClass::class);
        $class = $this->academicClassService->store($request->validated());
        
        if ($request->wantsJson()) {
            return $this->successResponse(new AcademicClassResource($class), 'Class created successfully', 201);
        }

        return redirect()->route('classes.index')->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function show(Request $request, $id)
    {
        $class = $this->academicClassService->getById($id, ['*'], ['academicYear', 'students']);
        
        if (!$class) {
            if ($request->wantsJson()) return $this->errorResponse('Class not found', 404);
            return redirect()->route('classes.index')->with('error', 'Kelas tidak ditemukan.');
        }

        Gate::authorize('view', $class);

        if ($request->wantsJson()) {
            return $this->successResponse(new AcademicClassResource($class), 'Class retrieved successfully');
        }

        return view('classes.show', compact('class'));
    }

    public function edit($id)
    {
        $class = $this->academicClassService->getById($id);
        
        if (!$class) {
            return redirect()->route('classes.index')->with('error', 'Kelas tidak ditemukan.');
        }

        Gate::authorize('update', $class);
        
        $academicYears = \App\Modules\Academic\Models\AcademicYear::orderBy('start_year', 'desc')->get();
        return view('classes.edit', compact('class', 'academicYears'));
    }

    public function update(UpdateAcademicClassRequest $request, $id)
    {
        $class = $this->academicClassService->getById($id);
        
        if (!$class) {
            if ($request->wantsJson()) return $this->errorResponse('Class not found', 404);
            return redirect()->route('classes.index')->with('error', 'Kelas tidak ditemukan.');
        }

        Gate::authorize('update', $class);

        $this->academicClassService->update($id, $request->validated());
        
        $updatedClass = $this->academicClassService->getById($id);

        if ($request->wantsJson()) {
            return $this->successResponse(new AcademicClassResource($updatedClass), 'Class updated successfully');
        }

        return redirect()->route('classes.index')->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $class = $this->academicClassService->getById($id);
        
        if (!$class) {
            if ($request->wantsJson()) return $this->errorResponse('Class not found', 404);
            return redirect()->route('classes.index')->with('error', 'Kelas tidak ditemukan.');
        }

        Gate::authorize('delete', $class);

        try {
            $this->academicClassService->remove($id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson()) {
                return $this->errorResponse(collect($e->errors())->first()[0], 400);
            }
            return redirect()->route('classes.index')->with('error', collect($e->errors())->first()[0]);
        }

        if ($request->wantsJson()) {
            return $this->successResponse(null, 'Class deleted successfully');
        }

        return redirect()->route('classes.index')->with('success', 'Kelas berhasil dihapus.');
    }

    public function restore(Request $request, $id)
    {
        $class = AcademicClass::withTrashed()->findOrFail($id);
        Gate::authorize('restore', $class);

        $this->academicClassService->restore($id);
        
        if ($request->wantsJson()) {
            return $this->successResponse(null, 'Class restored successfully');
        }

        return redirect()->route('classes.index')->with('success', 'Kelas berhasil dipulihkan.');
    }
}
