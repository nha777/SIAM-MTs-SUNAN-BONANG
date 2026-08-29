<?php

namespace App\Modules\Student\Services;

use App\Modules\Base\Services\BaseService;
use App\Modules\Student\Repositories\Contracts\GuardianRepositoryInterface;
use App\Modules\Student\Services\Contracts\GuardianServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GuardianService extends BaseService implements GuardianServiceInterface
{
    /**
     * GuardianRepository instance.
     */
    protected GuardianRepositoryInterface $guardianRepository;

    /**
     * GuardianService constructor.
     */
    public function __construct(GuardianRepositoryInterface $guardianRepository)
    {
        parent::__construct($guardianRepository);
        $this->guardianRepository = $guardianRepository;
    }

    /**
     * Menghapus profil wali murid (soft-delete).
     * Sesuai BR-010: Menonaktifkan wali murid otomatis menonaktifkan status siswa anaknya menjadi 'keluar'.
     */
    public function remove(int|string $id): bool
    {
        DB::beginTransaction();
        try {
            $guardian = $this->guardianRepository->find($id, ['*'], ['students']);
            
            if (!$guardian) {
                Log::warning('Percobaan menghapus wali murid gagal: data tidak ditemukan', [
                    'id' => $id
                ]);
                DB::rollBack();
                return false;
            }

            // Cascade deactivation: Ubah status seluruh siswa aktif di bawah wali ini menjadi 'keluar'
            foreach ($guardian->students as $student) {
                if (in_array($student->status, ['aktif', 'skorsing'])) {
                    $student->update(['status' => 'keluar']);
                    
                    Log::info("Siswa dinonaktifkan secara otomatis (cascade deactivation) akibat penghapusan wali", [
                        'student_id' => $student->id,
                        'guardian_id' => $guardian->id
                    ]);
                }
            }

            // Hapus wali (soft-delete)
            $status = $this->guardianRepository->delete($id);
            
            DB::commit();
            Log::info("Wali murid berhasil dihapus secara logis (soft-delete)", [
                'guardian_id' => $id
            ]);
            return $status;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus wali murid: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Memulihkan wali murid dari soft-delete.
     */
    public function restore(int|string $id): bool
    {
        DB::beginTransaction();
        try {
            $guardianModel = $this->guardianRepository->getModel();
            $guardian = $guardianModel->withTrashed()->find($id);

            if (!$guardian) {
                Log::warning('Percobaan memulihkan wali murid gagal: wali murid tidak ditemukan', [
                    'id' => $id
                ]);
                DB::rollBack();
                return false;
            }

            $restored = $guardian->restore();
            
            Log::info("Wali murid berhasil dipulihkan dari soft-delete", [
                'guardian_id' => $guardian->id
            ]);
            
            DB::commit();
            return $restored;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
