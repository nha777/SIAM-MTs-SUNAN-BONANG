<?php
namespace App\Modules\Academic\Services;

interface EnrollmentServiceInterface
{
    public function getAllEnrollments($academicYearId = null, $semesterId = null, $classId = null, $paginate = 15);
    public function getEnrollmentById($id);
    public function createEnrollment(array $data);
    public function updateEnrollment($id, array $data);
    public function deleteEnrollment($id);
}
