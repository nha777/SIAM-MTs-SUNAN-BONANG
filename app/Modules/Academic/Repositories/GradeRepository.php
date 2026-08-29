<?php
namespace App\Modules\Academic\Repositories;

use App\Modules\Academic\Models\Grade;

class GradeRepository implements GradeRepositoryInterface
{
    public function all($paginate = 15)
    {
        return Grade::with(['student', 'academicYear', 'semester', 'academicClass'])->latest()->paginate($paginate);
    }
    public function find($id)
    {
        return Grade::findOrFail($id);
    }
    public function create(array $data)
    {
        return Grade::create($data);
    }
    public function update($id, array $data)
    {
        $model = Grade::findOrFail($id);
        $model->update($data);
        return $model;
    }
    public function delete($id)
    {
        $model = Grade::findOrFail($id);
        return $model->delete();
    }
}
