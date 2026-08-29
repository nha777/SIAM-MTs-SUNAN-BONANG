<?php
namespace App\Modules\Academic\Repositories;

use App\Modules\Academic\Models\ReportCard;

class ReportCardRepository implements ReportCardRepositoryInterface
{
    public function all($paginate = 15)
    {
        return ReportCard::with(['student', 'academicYear', 'semester', 'academicClass'])->latest()->paginate($paginate);
    }
    public function find($id)
    {
        return ReportCard::findOrFail($id);
    }
    public function create(array $data)
    {
        return ReportCard::create($data);
    }
    public function update($id, array $data)
    {
        $model = ReportCard::findOrFail($id);
        $model->update($data);
        return $model;
    }
    public function delete($id)
    {
        $model = ReportCard::findOrFail($id);
        return $model->delete();
    }
}
