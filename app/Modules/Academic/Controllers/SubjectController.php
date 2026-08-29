<?php

namespace App\Modules\Academic\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Academic\Requests\SubjectRequest;
use App\Modules\Academic\Services\SubjectServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Modules\Base\Traits\BaseApiResponse;

class SubjectController extends Controller
{
    use BaseApiResponse;

    protected $service;

    public function __construct(SubjectServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        Gate::authorize('subject.view');
        
        $search = $request->input('search');
        $type = $request->input('type');
        
        $subjects = $this->service->getAllSubjects($search, $type);
        
        if ($request->wantsJson()) {
            return $this->successResponse($subjects, 'Subjects retrieved successfully.');
        }

        return view('academic::subjects.index', compact('subjects'));
    }

    public function create()
    {
        Gate::authorize('subject.create');
        return view('academic::subjects.create');
    }

    public function store(SubjectRequest $request)
    {
        Gate::authorize('subject.create');
        
        $subject = $this->service->createSubject($request->validated());

        if ($request->wantsJson()) {
            return $this->successResponse($subject, 'Mata Pelajaran berhasil ditambahkan.', 201);
        }

        return redirect()->route('subjects.index')->with('success', 'Mata Pelajaran berhasil ditambahkan.');
    }

    public function edit($id)
    {
        Gate::authorize('subject.update');
        
        $subject = $this->service->getSubjectById($id);
        
        return view('academic::subjects.edit', compact('subject'));
    }

    public function update(SubjectRequest $request, $id)
    {
        Gate::authorize('subject.update');
        
        $subject = $this->service->updateSubject($id, $request->validated());

        if ($request->wantsJson()) {
            return $this->successResponse($subject, 'Mata Pelajaran berhasil diperbarui.');
        }

        return redirect()->route('subjects.index')->with('success', 'Mata Pelajaran berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        Gate::authorize('subject.delete');
        
        $this->service->deleteSubject($id);

        if ($request->wantsJson()) {
            return $this->successResponse(null, 'Mata Pelajaran berhasil dihapus.');
        }

        return redirect()->route('subjects.index')->with('success', 'Mata Pelajaran berhasil dihapus.');
    }
}
