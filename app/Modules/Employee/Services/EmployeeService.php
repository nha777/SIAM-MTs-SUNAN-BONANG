<?php

namespace App\Modules\Employee\Services;

use App\Modules\Employee\Repositories\EmployeeRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeService implements EmployeeServiceInterface
{
    protected $repository;

    public function __construct(EmployeeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllEmployees($search = null, $position = null, $paginate = 15)
    {
        return $this->repository->all($search, $position, $paginate);
    }

    public function getEmployeeById($id)
    {
        return $this->repository->find($id);
    }

    public function createEmployee(array $data)
    {
        DB::beginTransaction();
        try {
            $createAccount = $data['create_user'] ?? false;
            
            if ($createAccount && !empty($data['email'])) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['nip'] ?? 'password123'),
                    'is_active' => $data['is_active'] ?? true,
                ]);
                
                // Default assign 'Guru' role if it exists (Optional, adjust as needed)
                $user->assignRole('Guru');

                $data['user_id'] = $user->id;
            }

            $result = $this->repository->create($data);
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateEmployee($id, array $data)
    {
        DB::beginTransaction();
        try {
            $employee = $this->repository->find($id);
            
            $createAccount = $data['create_user'] ?? false;
            
            if ($createAccount && !$employee->user_id && !empty($data['email'])) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['nip'] ?? 'password123'),
                    'is_active' => $data['is_active'] ?? true,
                ]);
                $user->assignRole('Guru');
                $data['user_id'] = $user->id;
            } elseif ($employee->user_id) {
                // Update existing user if needed
                if (isset($data['name']) || isset($data['email'])) {
                    $employee->user->update([
                        'name' => $data['name'] ?? $employee->user->name,
                        'email' => $data['email'] ?? $employee->user->email,
                        'is_active' => $data['is_active'] ?? $employee->user->is_active,
                    ]);
                }
            }

            $result = $this->repository->update($id, $data);
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteEmployee($id)
    {
        return $this->repository->delete($id);
    }
}
