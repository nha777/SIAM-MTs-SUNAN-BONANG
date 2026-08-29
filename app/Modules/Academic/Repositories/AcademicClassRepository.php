<?php

namespace App\Modules\Academic\Repositories;

use App\Modules\Base\Repositories\BaseRepository;
use App\Modules\Academic\Models\AcademicClass;
use App\Modules\Academic\Repositories\Contracts\AcademicClassRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AcademicClassRepository extends BaseRepository implements AcademicClassRepositoryInterface
{
    /**
     * AcademicClassRepository Constructor.
     */
    public function __construct(AcademicClass $model)
    {
        parent::__construct($model);
    }

    /**
     * Mendapatkan daftar kelas fisik berdasarkan ID semester.
     */
    public function getClassesBySemester(int $semesterId): Collection
    {
        return $this->model->where('semester_id', $semesterId)->get();
    }

    /**
     * Mendapatkan daftar kelas berdasarkan tingkat jenjang (7, 8, atau 9).
     */
    public function getClassesByGrade(int $grade): Collection
    {
        return $this->model->where('grade', $grade)->get();
    }
}
