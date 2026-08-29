<?php

namespace App\Modules\Employee\Repositories;

use App\Modules\Employee\Models\Employee;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    public function all($search = null, $position = null, $paginate = 15)
    {
        $query = Employee::with('user');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nuptk', 'like', "%{$search}%");
            });
        }

        if ($position) {
            $query->where('position', $position);
        }

        return $query->latest()->paginate($paginate);
    }

    public function find($id)
    {
        return Employee::findOrFail($id);
    }

    public function create(array $data)
    {
        return Employee::create($data);
    }

    public function update($id, array $data)
    {
        $employee = Employee::findOrFail($id);
        $employee->update($data);
        return $employee;
    }

    public function delete($id)
    {
        $employee = Employee::findOrFail($id);
        return $employee->delete();
    }
}
