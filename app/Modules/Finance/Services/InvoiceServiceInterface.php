<?php
namespace App\Modules\Finance\Services;

interface InvoiceServiceInterface
{
    public function getAll($paginate = 15);
    public function getById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
