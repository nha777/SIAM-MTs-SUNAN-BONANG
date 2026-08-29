<?php

namespace App\Modules\Academic\Repositories\Contracts;

use App\Modules\Base\Contracts\BaseRepositoryInterface;
use App\Modules\Academic\Models\AcademicYear;

interface AcademicYearRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Mendapatkan tahun ajaran aktif tunggal saat ini.
     */
    public function getActiveAcademicYear(): ?AcademicYear;

    /**
     * Nonaktifkan semua status tahun ajaran aktif di database.
     */
    public function deactivateAll(): bool;
}
