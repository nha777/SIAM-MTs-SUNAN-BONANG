<?php

namespace App\Modules\Employee\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Employee\Requests\EmployeeRequest;
use App\Modules\Employee\Services\EmployeeServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Modules\Base\Traits\BaseApiResponse;

class EmployeeController extends Controller
{
    use BaseApiResponse;

    protected $service;

    public function __construct(EmployeeServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        Gate::authorize('employee.view');
        
        $search = $request->input('search');
        $position = $request->input('position');
        
        $employees = $this->service->getAllEmployees($search, $position);
        
        if ($request->wantsJson()) {
            return $this->successResponse($employees, 'Employees retrieved successfully.');
        }

        return view('employee::employees.index', compact('employees'));
    }

    public function create()
    {
        Gate::authorize('employee.create');
        return view('employee::employees.create');
    }

    public function store(EmployeeRequest $request)
    {
        Gate::authorize('employee.create');
        
        $employee = $this->service->createEmployee($request->validated());

        if ($request->wantsJson()) {
            return $this->successResponse($employee, 'Pegawai berhasil ditambahkan.', 201);
        }

        return redirect()->route('employees.index')->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    public function show(Request $request, $id)
    {
        Gate::authorize('employee.view');
        
        $employee = $this->service->getEmployeeById($id);

        if ($request->wantsJson()) {
            return $this->successResponse($employee, 'Employee retrieved successfully.');
        }

        return view('employee::employees.show', compact('employee'));
    }

    public function edit($id)
    {
        Gate::authorize('employee.update');
        
        $employee = $this->service->getEmployeeById($id);
        
        return view('employee::employees.edit', compact('employee'));
    }

    public function update(EmployeeRequest $request, $id)
    {
        Gate::authorize('employee.update');
        
        $employee = $this->service->updateEmployee($id, $request->validated());

        if ($request->wantsJson()) {
            return $this->successResponse($employee, 'Pegawai berhasil diperbarui.');
        }

        return redirect()->route('employees.index')->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        Gate::authorize('employee.delete');
        
        $this->service->deleteEmployee($id);

        if ($request->wantsJson()) {
            return $this->successResponse(null, 'Pegawai berhasil dihapus.');
        }

        return redirect()->route('employees.index')->with('success', 'Data pegawai berhasil dihapus.');
    }
}
