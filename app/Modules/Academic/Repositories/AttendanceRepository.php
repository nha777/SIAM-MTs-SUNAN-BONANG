<?php
namespace App\Modules\Academic\Repositories;

use App\Modules\Academic\Models\Attendance;

class AttendanceRepository implements AttendanceRepositoryInterface
{
    public function all($paginate = 15)
    {
        return Attendance::with(['student', 'academicYear', 'semester', 'academicClass'])->latest()->paginate($paginate);
    }
    public function find($id)
    {
        return Attendance::findOrFail($id);
    }
    public function create(array $data)
    {
        return Attendance::create($data);
    }
    public function update($id, array $data)
    {
        $model = Attendance::findOrFail($id);
        $model->update($data);
        return $model;
    }
    public function delete($id)
    {
        $model = Attendance::findOrFail($id);
        return $model->delete();
    }
}
