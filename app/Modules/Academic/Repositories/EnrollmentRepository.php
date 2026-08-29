<?php
namespace App\Modules\Academic\Repositories;

use App\Modules\Academic\Models\Enrollment;

class EnrollmentRepository implements EnrollmentRepositoryInterface
{
    public function all($academicYearId = null, $semesterId = null, $classId = null, $paginate = 15)
    {
        $query = Enrollment::with(['student', 'academicYear', 'semester', 'academicClass']);

        if ($academicYearId) $query->where('academic_year_id', $academicYearId);
        if ($semesterId) $query->where('semester_id', $semesterId);
        if ($classId) $query->where('academic_class_id', $classId);

        return $query->orderBy('enrollment_date', 'desc')->paginate($paginate);
    }

    public function find($id)
    {
        return Enrollment::findOrFail($id);
    }

    public function create(array $data)
    {
        return Enrollment::create($data);
    }

    public function update($id, array $data)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->update($data);
        return $enrollment;
    }

    public function delete($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        return $enrollment->delete();
    }
}
