<?php
namespace App\Modules\Academic\Services;

use App\Modules\Academic\Repositories\EnrollmentRepositoryInterface;

class EnrollmentService implements EnrollmentServiceInterface
{
    protected $repository;

    public function __construct(EnrollmentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllEnrollments($academicYearId = null, $semesterId = null, $classId = null, $paginate = 15)
    {
        return $this->repository->all($academicYearId, $semesterId, $classId, $paginate);
    }

    public function getEnrollmentById($id)
    {
        return $this->repository->find($id);
    }

    public function createEnrollment(array $data)
    {
        return $this->repository->create($data);
    }

    public function updateEnrollment($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function deleteEnrollment($id)
    {
        return $this->repository->delete($id);
    }
}
