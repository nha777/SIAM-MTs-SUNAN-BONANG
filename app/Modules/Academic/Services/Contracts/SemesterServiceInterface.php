<?php

namespace App\Modules\Academic\Services\Contracts;

use App\Modules\Base\Contracts\BaseServiceInterface;

interface SemesterServiceInterface extends BaseServiceInterface
{
    /**
     * Mengaktifkan semester tertentu dan menonaktifkan semester lainnya.
     */
    public function activate(int|string $id): bool;

    /**
     * Memulihkan semester yang di-soft delete.
     */
    public function restore(int|string $id): bool;
}
