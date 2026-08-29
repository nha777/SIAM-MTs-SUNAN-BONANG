<?php
namespace App\Modules\Academic\Repositories;

interface ReportCardRepositoryInterface
{
    public function all($paginate = 15);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
