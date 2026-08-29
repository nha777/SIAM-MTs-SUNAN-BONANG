<?php

namespace App\Modules\Academic\Services;

use App\Modules\Base\Services\BaseService;
use App\Modules\Academic\Repositories\Contracts\SemesterRepositoryInterface;
use App\Modules\Academic\Repositories\Contracts\AcademicYearRepositoryInterface;
use App\Modules\Academic\Services\Contracts\SemesterServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SemesterService extends BaseService implements SemesterServiceInterface
{
    /**
     * SemesterRepository instance.
     */
    protected SemesterRepositoryInterface $semesterRepository;
    protected AcademicYearRepositoryInterface $academicYearRepository;

    /**
     * SemesterService constructor.
     */
    public function __construct(
        SemesterRepositoryInterface $semesterRepository,
        AcademicYearRepositoryInterface $academicYearRepository
    ) {
        parent::__construct($semesterRepository);
        $this->semesterRepository = $semesterRepository;
        $this->academicYearRepository = $academicYearRepository;
    }

    public function store(array $data): \Illuminate\Database\Eloquent\Model
    {
        DB::beginTransaction();
        try {
            if (!empty($data['is_active'])) {
                $this->semesterRepository->deactivateAll();
                $this->academicYearRepository->deactivateAll();
                $this->academicYearRepository->update($data['academic_year_id'], ['is_active' => true]);
            }
            $result = parent::store($data);
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int|string $id, array $data): bool
    {
        DB::beginTransaction();
        try {
            if (!empty($data['is_active'])) {
                $this->semesterRepository->deactivateAll();
                $semester = $this->semesterRepository->find($id);
                $academicYearId = $data['academic_year_id'] ?? $semester->academic_year_id;
                
                $this->academicYearRepository->deactivateAll();
                $this->academicYearRepository->update($academicYearId, ['is_active' => true]);
            }
            $result = parent::update($id, $data);
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Mengaktifkan semester tertentu dan menonaktifkan semester lainnya.
     * Mencegah dual-active concurrency risk via Pessimistic Row Locking.
     */
    public function activate(int|string $id): bool
    {
        DB::beginTransaction();
        try {
            // 1. Ambil dan kunci baris target untuk memastikan record ada sebelum melakukan deaktivasi massal
            $semester = $this->semesterRepository->getModel()->newQuery()->lockForUpdate()->find($id);

            if (!$semester) {
                Log::warning('Percobaan mengaktifkan semester gagal: data tidak ditemukan', [
                    'id' => $id
                ]);
                DB::rollBack();
                return false;
            }

            // 2. Ubah status keaktifan seluruh semester lain menjadi non-aktif
            $this->semesterRepository->deactivateAll();
            
            // 3. Ubah status keaktifan seluruh tahun ajaran lain menjadi non-aktif
            $this->academicYearRepository->deactivateAll();
            
            // 4. Aktifkan tahun ajaran induk
            $this->academicYearRepository->update($semester->academic_year_id, ['is_active' => true]);

            // 5. Aktifkan semester yang dipilih
            $activated = $this->semesterRepository->update($id, ['is_active' => true]);

            Log::info("Semester berhasil diaktifkan secara tunggal (beserta Tahun Ajaran)", [
                'semester_id' => $id,
                'academic_year_id' => $semester->academic_year_id
            ]);

            DB::commit();
            return $activated;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Memulihkan semester dari soft-delete.
     */
    public function restore(int|string $id): bool
    {
        DB::beginTransaction();
        try {
            $restored = $this->semesterRepository->restore($id);
            
            if ($restored) {
                Log::info("Semester berhasil dipulihkan dari soft-delete", [
                    'semester_id' => $id
                ]);
            } else {
                Log::warning('Percobaan memulihkan semester gagal: tidak ditemukan atau tidak di-trash', [
                    'id' => $id
                ]);
            }
            
            DB::commit();
            return $restored;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
