<?php

namespace App\Modules\Student\Repositories\Contracts;

use App\Modules\Base\Contracts\BaseRepositoryInterface;
use App\Modules\Student\Models\Student;
use Illuminate\Database\Eloquent\Collection;

interface StudentRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Cari siswa berdasarkan NISN aktif.
     */
    public function findByNisn(string $nisn): ?Student;

    /**
     * Mendapatkan semua siswa aktif berdasarkan ID kelas.
     */
    public function getStudentsByClass(int $classId): Collection;

    /**
     * Mendapatkan semua siswa berdasarkan status tertentu.
     */
    public function getStudentsByStatus(string $status): Collection;
}
