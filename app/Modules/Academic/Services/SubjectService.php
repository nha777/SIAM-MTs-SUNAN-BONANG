<?php

namespace App\Modules\Academic\Services;

use App\Modules\Academic\Repositories\SubjectRepositoryInterface;

class SubjectService implements SubjectServiceInterface
{
    protected $repository;

    public function __construct(SubjectRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllSubjects($search = null, $type = null, $paginate = 15)
    {
        return $this->repository->all($search, $type, $paginate);
    }

    public function getSubjectById($id)
    {
        return $this->repository->find($id);
    }

    public function createSubject(array $data)
    {
        return $this->repository->create($data);
    }

    public function updateSubject($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function deleteSubject($id)
    {
        return $this->repository->delete($id);
    }
}
