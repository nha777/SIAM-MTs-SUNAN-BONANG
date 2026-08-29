<?php
namespace App\Modules\Academic\Repositories;

interface EnrollmentRepositoryInterface
{
    public function all($academicYearId = null, $semesterId = null, $classId = null, $paginate = 15);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
