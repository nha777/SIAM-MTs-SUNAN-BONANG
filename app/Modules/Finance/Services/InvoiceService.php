<?php
namespace App\Modules\Finance\Services;

use App\Modules\Finance\Repositories\InvoiceRepositoryInterface;

class InvoiceService implements InvoiceServiceInterface
{
    protected $repository;
    public function __construct(InvoiceRepositoryInterface $repository) { $this->repository = $repository; }
    public function getAll($paginate = 15) { return $this->repository->all($paginate); }
    public function getById($id) { return $this->repository->find($id); }
    public function create(array $data) {
        if (empty($data['invoice_number']) || str_starts_with($data['invoice_number'], 'INV-17')) {
            $data['invoice_number'] = $this->generateInvoiceNumber();
        } return $this->repository->create($data); }
    public function update($id, array $data) { return $this->repository->update($id, $data); }
    public function delete($id) { return $this->repository->delete($id); }
}
