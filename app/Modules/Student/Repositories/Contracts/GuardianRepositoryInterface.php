<?php

namespace App\Modules\Student\Repositories\Contracts;

use App\Modules\Base\Contracts\BaseRepositoryInterface;
use App\Modules\Student\Models\Guardian;

interface GuardianRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Cari wali murid berdasarkan nomor telepon aktif.
     */
    public function findByPhoneNumber(string $phoneNumber): ?Guardian;

    /**
     * Cari wali murid berdasarkan user ID.
     */
    public function findByUserId(int $userId): ?Guardian;
}
