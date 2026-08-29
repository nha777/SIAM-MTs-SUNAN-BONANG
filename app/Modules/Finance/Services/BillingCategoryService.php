<?php
namespace App\Modules\Finance\Services;

use App\Modules\Finance\Repositories\BillingCategoryRepositoryInterface;

class BillingCategoryService implements BillingCategoryServiceInterface
{
    protected $repository;
    public function __construct(BillingCategoryRepositoryInterface $repository) { $this->repository = $repository; }
    public function getAll($paginate = 15) { return $this->repository->all($paginate); }
    public function getById($id) { return $this->repository->find($id); }
    public function create(array $data) { return $this->repository->create($data); }
    public function update($id, array $data) { return $this->repository->update($id, $data); }
    public function delete($id) { return $this->repository->delete($id); }
}
