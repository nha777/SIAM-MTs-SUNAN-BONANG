<?php
namespace App\Modules\Finance\Services;

use App\Modules\Finance\Repositories\PaymentRepositoryInterface;

class PaymentService implements PaymentServiceInterface
{
    protected $repository;
    public function __construct(PaymentRepositoryInterface $repository) { $this->repository = $repository; }
    public function create(array $data) { return $this->repository->create($data); }
    public function update($id, array $data) { return $this->repository->update($id, $data); }
    public function delete($id) { return $this->repository->delete($id); }
    public function getPendingVerifications($paginate = 15) { return $this->repository->getPendingVerifications($paginate); }
}
