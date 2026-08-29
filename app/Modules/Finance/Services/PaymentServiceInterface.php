<?php
namespace App\Modules\Finance\Services;

interface PaymentServiceInterface
{
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getPendingVerifications($paginate = 15);
}
