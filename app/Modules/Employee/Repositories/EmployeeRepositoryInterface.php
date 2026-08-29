<?php

namespace App\Modules\Employee\Repositories;

interface EmployeeRepositoryInterface
{
    public function all($search = null, $position = null, $paginate = 15);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
