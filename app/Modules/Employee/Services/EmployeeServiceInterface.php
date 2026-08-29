<?php

namespace App\Modules\Employee\Services;

interface EmployeeServiceInterface
{
    public function getAllEmployees($search = null, $position = null, $paginate = 15);
    public function getEmployeeById($id);
    public function createEmployee(array $data);
    public function updateEmployee($id, array $data);
    public function deleteEmployee($id);
}
