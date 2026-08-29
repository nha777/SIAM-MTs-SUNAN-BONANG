<?php

namespace App\Modules\Student\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Student\Models\Guardian;
use App\Modules\Student\Requests\StoreGuardianRequest;
use App\Modules\Student\Requests\UpdateGuardianRequest;
use App\Modules\Student\Services\Contracts\GuardianServiceInterface;
use App\Modules\Student\Resources\GuardianResource;
use App\Modules\Base\Traits\BaseApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class GuardianController extends Controller
{
    use BaseApiResponse;

    /**
     * Instance of GuardianServiceInterface.
     */
    protected GuardianServiceInterface $guardianService;

    /**
     * GuardianController constructor.
     */
    public function __construct(GuardianServiceInterface $guardianService)
    {
        $this->guardianService = $guardianService;
    }

    /**
     * Menampilkan daftar wali murid.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Guardian::class);
        
        $search = $request->input('search');
        $status = $request->input('status');
        
        $query = Guardian::withCount('students');
        
        if ($status === 'all') {
            $query->withTrashed();
        } elseif ($status === 'deleted') {
            $query->onlyTrashed();
        }
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('guardian_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }
        
        $guardians = $query->paginate(10)->withQueryString();

        if ($request->wantsJson()) {
            return $this->successResponse(GuardianResource::collection($guardians), 'Guardians retrieved successfully');
        }

        return view('guardians.index', compact('guardians'));
    }

    /**
     * Tampilkan form pembuatan wali murid baru.
     */
    public function create()
    {
        Gate::authorize('create', Guardian::class);
        return view('guardians.create');
    }

    /**
     * Menyimpan data wali murid baru.
     */
    public function store(StoreGuardianRequest $request)
    {
        Gate::authorize('create', Guardian::class);
        $guardian = $this->guardianService->store($request->validated());

        if ($request->wantsJson()) {
            return $this->successResponse(new GuardianResource($guardian), 'Guardian created successfully', 201);
        }

        return redirect()->route('guardians.index')->with('success', 'Data wali murid berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail wali murid tertentu.
     */
    public function show(Request $request, $id)
    {
        $guardian = $this->guardianService->getById($id, ['*'], ['students']);
        
        if (!$guardian) {
            if ($request->wantsJson()) {
                return $this->errorResponse('Guardian not found', 404);
            }
            return redirect()->route('guardians.index')->with('error', 'Wali murid tidak ditemukan.');
        }
        
        Gate::authorize('view', $guardian);
        
        if ($request->wantsJson()) {
            return $this->successResponse(new GuardianResource($guardian), 'Guardian retrieved successfully');
        }

        return view('guardians.show', compact('guardian'));
    }

    /**
     * Tampilkan form edit wali murid.
     */
    public function edit($id)
    {
        $guardian = $this->guardianService->getById($id);
        
        if (!$guardian) {
            return redirect()->route('guardians.index')->with('error', 'Wali murid tidak ditemukan.');
        }
        
        Gate::authorize('update', $guardian);
        
        return view('guardians.edit', compact('guardian'));
    }

    /**
     * Memperbarui data wali murid tertentu.
     */
    public function update(UpdateGuardianRequest $request, $id)
    {
        $guardian = $this->guardianService->getById($id);
        
        if (!$guardian) {
            if ($request->wantsJson()) {
                return $this->errorResponse('Guardian not found', 404);
            }
            return redirect()->route('guardians.index')->with('error', 'Wali murid tidak ditemukan.');
        }
        
        Gate::authorize('update', $guardian);
        $this->guardianService->update($id, $request->validated());
        
        $updatedGuardian = $this->guardianService->getById($id);
        
        if ($request->wantsJson()) {
            return $this->successResponse(new GuardianResource($updatedGuardian), 'Guardian updated successfully');
        }

        return redirect()->route('guardians.index')->with('success', 'Data wali murid berhasil diperbarui.');
    }

    /**
     * Menghapus data wali murid tertentu.
     */
    public function destroy(Request $request, $id)
    {
        $guardian = $this->guardianService->getById($id);
        
        if (!$guardian) {
            if ($request->wantsJson()) {
                return $this->errorResponse('Guardian not found', 404);
            }
            return redirect()->route('guardians.index')->with('error', 'Wali murid tidak ditemukan.');
        }
        
        Gate::authorize('delete', $guardian);
        $this->guardianService->remove($id);
        
        if ($request->wantsJson()) {
            return $this->successResponse(null, 'Guardian deleted successfully');
        }

        return redirect()->route('guardians.index')->with('success', 'Wali murid berhasil dihapus.');
    }

    /**
     * Memulihkan data wali murid dari soft-delete.
     */
    public function restore(Request $request, $id)
    {
        $guardian = Guardian::withTrashed()->findOrFail($id);
        Gate::authorize('restore', $guardian);
        
        $this->guardianService->restore($id);
        
        if ($request->wantsJson()) {
            return $this->successResponse(null, 'Guardian restored successfully');
        }

        return redirect()->route('guardians.index')->with('success', 'Wali murid berhasil dipulihkan.');
    }
}
