<?php

namespace App\Modules\Academic\Repositories;

use App\Modules\Base\Repositories\BaseRepository;
use App\Modules\Academic\Models\AcademicYear;
use App\Modules\Academic\Repositories\Contracts\AcademicYearRepositoryInterface;

class AcademicYearRepository extends BaseRepository implements AcademicYearRepositoryInterface
{
    /**
     * AcademicYearRepository Constructor.
     */
    public function __construct(AcademicYear $model)
    {
        parent::__construct($model);
    }

    /**
     * Mendapatkan tahun ajaran aktif tunggal saat ini.
     */
    public function getActiveAcademicYear(): ?AcademicYear
    {
        return $this->model->where('is_active', true)->first();
    }

    /**
     * Nonaktifkan semua status tahun ajaran aktif di database.
     */
    public function deactivateAll(): bool
    {
        $activeYears = $this->model->where('is_active', true)->get();
        $success = true;
        foreach ($activeYears as $year) {
            if (!$year->update(['is_active' => false])) {
                $success = false;
            }
        }
        return $success;
    }
}
