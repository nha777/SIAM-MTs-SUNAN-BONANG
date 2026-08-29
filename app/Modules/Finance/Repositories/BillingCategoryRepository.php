<?php
namespace App\Modules\Finance\Repositories;

use App\Modules\Finance\Models\BillingCategory;

class BillingCategoryRepository implements BillingCategoryRepositoryInterface
{
    public function all($paginate = 15) { return BillingCategory::latest()->paginate($paginate); }
    public function find($id) { return BillingCategory::findOrFail($id); }
    public function create(array $data) { return BillingCategory::create($data); }
    public function update($id, array $data) {
        $model = BillingCategory::findOrFail($id);
        $model->update($data);
        return $model;
    }
    public function delete($id) { return BillingCategory::findOrFail($id)->delete(); }
}
