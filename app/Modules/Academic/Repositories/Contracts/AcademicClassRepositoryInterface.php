<?php

namespace App\Modules\Academic\Repositories\Contracts;

use App\Modules\Base\Contracts\BaseRepositoryInterface;
use App\Modules\Academic\Models\AcademicClass;
use Illuminate\Database\Eloquent\Collection;

interface AcademicClassRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Mendapatkan daftar kelas fisik berdasarkan ID semester.
     */
    public function getClassesBySemester(int $semesterId): Collection;

    /**
     * Mendapatkan daftar kelas berdasarkan tingkat jenjang (7, 8, atau 9).
     */
    public function getClassesByGrade(int $grade): Collection;
}
