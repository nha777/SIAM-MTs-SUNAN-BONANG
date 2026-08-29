<?php

namespace App\Modules\Academic\Services;

interface SubjectServiceInterface
{
    public function getAllSubjects($search = null, $type = null, $paginate = 15);
    public function getSubjectById($id);
    public function createSubject(array $data);
    public function updateSubject($id, array $data);
    public function deleteSubject($id);
}
