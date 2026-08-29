<?php

namespace App\Modules\Academic\Repositories;

use App\Modules\Base\Repositories\BaseRepository;
use App\Modules\Academic\Models\Semester;
use App\Modules\Academic\Repositories\Contracts\SemesterRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SemesterRepository extends BaseRepository implements SemesterRepositoryInterface
{
    /**
     * SemesterRepository Constructor.
     */
    public function __construct(Semester $model)
    {
        parent::__construct($model);
    }

    /**
     * Mendapatkan semester aktif tunggal saat ini.
     */
    public function getActiveSemester(): ?Semester
    {
        return $this->model->where('is_active', true)->first();
    }

    /**
     * Mendapatkan daftar semester berdasarkan ID tahun ajaran.
     */
    public function getByAcademicYear(int $academicYearId): Collection
    {
        return $this->model->where('academic_year_id', $academicYearId)->get();
    }

    /**
     * Nonaktifkan semua status semester aktif di database.
     */
    public function deactivateAll(): bool
    {
        $activeSemesters = $this->model->where('is_active', true)->get();
        $success = true;
        foreach ($activeSemesters as $semester) {
            if (!$semester->update(['is_active' => false])) {
                $success = false;
            }
        }
        return $success;
    }
}
