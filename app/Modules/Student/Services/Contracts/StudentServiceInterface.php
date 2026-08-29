<?php

namespace App\Modules\Student\Services\Contracts;

use App\Modules\Base\Contracts\BaseServiceInterface;
use App\Modules\Student\Models\Student;

interface StudentServiceInterface extends BaseServiceInterface
{
    /**
     * Mendaftarkan siswa baru beserta wali muridnya dalam satu transaksi atomik.
     */
    public function registerWithGuardian(array $studentData, array $guardianData): Student;

    /**
     * Memulihkan siswa dari soft-delete setelah memvalidasi keunikan NISN aktif.
     */
    public function restore(int|string $id): bool;
}
