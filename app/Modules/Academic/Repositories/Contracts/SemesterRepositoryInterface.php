<?php

namespace App\Modules\Academic\Repositories\Contracts;

use App\Modules\Base\Contracts\BaseRepositoryInterface;
use App\Modules\Academic\Models\Semester;
use Illuminate\Database\Eloquent\Collection;

interface SemesterRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Mendapatkan semester aktif tunggal saat ini.
     */
    public function getActiveSemester(): ?Semester;

    /**
     * Mendapatkan daftar semester berdasarkan ID tahun ajaran.
     */
    public function getByAcademicYear(int $academicYearId): Collection;

    /**
     * Nonaktifkan semua status semester aktif di database.
     */
    public function deactivateAll(): bool;
}
