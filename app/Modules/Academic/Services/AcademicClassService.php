<?php

namespace App\Modules\Academic\Services;

use App\Modules\Base\Services\BaseService;
use App\Modules\Academic\Repositories\Contracts\AcademicClassRepositoryInterface;
use App\Modules\Academic\Services\Contracts\AcademicClassServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AcademicClassService extends BaseService implements AcademicClassServiceInterface
{
    /**
     * AcademicClassRepository instance.
     */
    protected AcademicClassRepositoryInterface $academicClassRepository;

    /**
     * AcademicClassService constructor.
     */
    public function __construct(AcademicClassRepositoryInterface $academicClassRepository)
    {
        parent::__construct($academicClassRepository);
        $this->academicClassRepository = $academicClassRepository;
    }

    /**
     * Menghapus kelas fisik (soft-delete).
     * Mencegah "Siswa Tanpa Kelas" dengan melarang penghapusan kelas yang masih menampung siswa aktif.
     */
    public function remove(int|string $id): bool
    {
        DB::beginTransaction();

        try {
            // 1. Ambil kelas dan kunci baris untuk mencegah modifikasi/penghapusan konkuren
            $class = $this->academicClassRepository->getModel()
                ->newQuery()
                ->lockForUpdate()
                ->find($id);
            
            if (!$class) {
                Log::warning('Percobaan menghapus kelas gagal: data tidak ditemukan', [
                    'id' => $id
                ]);
                DB::rollBack();
                return false;
            }

            // 2. Kunci seluruh relasi siswa yang berada di kelas ini dengan status aktif/skorsing untuk mencegah TOCTOU race condition
            $hasActiveStudents = $class->students()
                ->whereIn('status', ['aktif', 'skorsing'])
                ->lockForUpdate()
                ->exists();

            if ($hasActiveStudents) {
                Log::warning('Proses penghapusan kelas ditolak karena masih memiliki siswa aktif', [
                    'class_id' => $id,
                    'class_name' => $class->name
                ]);

                throw ValidationException::withMessages([
                    'class' => ['Kelas dilarang dihapus karena masih dihuni oleh siswa aktif.'],
                ]);
            }

            // 3. Lakukan penghapusan kelas secara logis (soft-delete)
            // Use repository->delete directly to avoid nested transaction from parent::remove
            $status = $this->academicClassRepository->delete($id);

            Log::info("Kelas berhasil dihapus secara logis (soft-delete)", [
                'class_id' => $id,
                'class_name' => $class->name
            ]);

            DB::commit();
            return $status;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Mengembalikan kelas yang telah dihapus (soft-delete).
     */
    public function restore(int|string $id): bool
    {
        DB::beginTransaction();

        try {
            $status = $this->academicClassRepository->restore($id);
            
            Log::info("Kelas berhasil dipulihkan (restore)", [
                'class_id' => $id,
            ]);
            
            DB::commit();
            return $status;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
