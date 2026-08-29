<?php

namespace App\Modules\Academic\Services\Contracts;

use App\Modules\Base\Contracts\BaseServiceInterface;

interface AcademicYearServiceInterface extends BaseServiceInterface
{
    /**
     * Mengaktifkan tahun ajaran tertentu dan menonaktifkan tahun ajaran lainnya.
     */
    public function activate(int|string $id): bool;

    /**
     * Memulihkan entitas bisnis yang di-soft delete.
     */
    public function restore(int|string $id): bool;
}
