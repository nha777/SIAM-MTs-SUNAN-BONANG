<?php

namespace App\Modules\Student\Repositories;

use App\Modules\Base\Repositories\BaseRepository;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Repositories\Contracts\StudentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class StudentRepository extends BaseRepository implements StudentRepositoryInterface
{
    /**
     * StudentRepository Constructor.
     */
    public function __construct(Student $model)
    {
        parent::__construct($model);
    }

    /**
     * Cari siswa berdasarkan NISN aktif.
     */
    public function findByNisn(string $nisn): ?Student
    {
        return $this->model->where('nisn', $nisn)->first();
    }

    /**
     * Mendapatkan semua siswa aktif berdasarkan ID kelas.
     */
    public function getStudentsByClass(int $classId): Collection
    {
        return $this->model->where('class_id', $classId)->get();
    }

    /**
     * Mendapatkan semua siswa berdasarkan status tertentu.
     */
    public function getStudentsByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }
}
