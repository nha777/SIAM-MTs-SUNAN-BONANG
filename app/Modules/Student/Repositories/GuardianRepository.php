<?php

namespace App\Modules\Student\Repositories;

use App\Modules\Base\Repositories\BaseRepository;
use App\Modules\Student\Models\Guardian;
use App\Modules\Student\Repositories\Contracts\GuardianRepositoryInterface;

class GuardianRepository extends BaseRepository implements GuardianRepositoryInterface
{
    /**
     * GuardianRepository Constructor.
     */
    public function __construct(Guardian $model)
    {
        parent::__construct($model);
    }

    /**
     * Cari wali murid berdasarkan nomor telepon aktif.
     */
    public function findByPhoneNumber(string $phoneNumber): ?Guardian
    {
        return $this->model->where('phone_number', $phoneNumber)->first();
    }

    /**
     * Cari wali murid berdasarkan user ID.
     */
    public function findByUserId(int $userId): ?Guardian
    {
        return $this->model->where('user_id', $userId)->first();
    }
}
