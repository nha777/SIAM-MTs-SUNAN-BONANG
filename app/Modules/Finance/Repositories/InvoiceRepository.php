<?php
namespace App\Modules\Finance\Repositories;

use App\Modules\Finance\Models\Invoice;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function all($paginate = 15)
    {
        return Invoice::with(['student', 'academicYear'])->latest()->paginate($paginate);
    }
    public function find($id)
    {
        return Invoice::with(['student', 'academicYear', 'payments', 'payments.recordedBy'])->findOrFail($id);
    }
    public function create(array $data)
    {
        return Invoice::create($data);
    }
    public function update($id, array $data)
    {
        $model = Invoice::findOrFail($id);
        $model->update($data);
        return $model;
    }
    public function delete($id)
    {
        $model = Invoice::findOrFail($id);
        return $model->delete();
    }
}
