<?php

namespace App\Modules\Student\Services\Contracts;

use App\Modules\Base\Contracts\BaseServiceInterface;

interface GuardianServiceInterface extends BaseServiceInterface
{
    /**
     * Memulihkan data wali murid (soft-delete).
     */
    public function restore(int|string $id): bool;
}
