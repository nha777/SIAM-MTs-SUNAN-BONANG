<?php

namespace App\Modules\Academic\Services;

use App\Modules\Base\Services\BaseService;
use App\Modules\Academic\Repositories\Contracts\AcademicYearRepositoryInterface;
use App\Modules\Academic\Services\Contracts\AcademicYearServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AcademicYearService extends BaseService implements AcademicYearServiceInterface
{
    /**
     * AcademicYearRepository instance.
     */
    protected AcademicYearRepositoryInterface $academicYearRepository;

    /**
     * AcademicYearService constructor.
     */
    public function __construct(AcademicYearRepositoryInterface $academicYearRepository)
    {
        parent::__construct($academicYearRepository);
        $this->academicYearRepository = $academicYearRepository;
    }

    public function store(array $data): \Illuminate\Database\Eloquent\Model
    {
        DB::beginTransaction();
        try {
            if (!empty($data['is_active'])) {
                $this->academicYearRepository->deactivateAll();
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
                $this->academicYearRepository->deactivateAll();
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
     * Mengaktifkan tahun ajaran tertentu dan menonaktifkan tahun ajaran lainnya.
     * Mencegah dual-active concurrency risk via Pessimistic Row Locking.
     */
    public function activate(int|string $id): bool
    {
        DB::beginTransaction();
        try {
            // 1. Ambil dan kunci baris target untuk memastikan record ada sebelum melakukan deaktivasi massal
            $academicYear = $this->academicYearRepository->getModel()->newQuery()->lockForUpdate()->find($id);

            if (!$academicYear) {
                Log::warning('Percobaan mengaktifkan tahun ajaran gagal: data tidak ditemukan', [
                    'id' => $id
                ]);
                DB::rollBack();
                return false;
            }

            // 2. Ubah status keaktifan seluruh tahun ajaran lain menjadi non-aktif (false)
            $this->academicYearRepository->deactivateAll();

            // 3. Aktifkan tahun ajaran yang dipilih
            $activated = $this->academicYearRepository->update($id, ['is_active' => true]);

            Log::info("Tahun Ajaran berhasil diaktifkan secara tunggal", [
                'academic_year_id' => $id
            ]);

            DB::commit();
            return $activated;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Memulihkan tahun ajaran dari soft-delete.
     */
    public function restore(int|string $id): bool
    {
        DB::beginTransaction();
        try {
            $restored = $this->academicYearRepository->restore($id);
            
            if ($restored) {
                Log::info("Tahun Ajaran berhasil dipulihkan dari soft-delete", [
                    'academic_year_id' => $id
                ]);
            } else {
                Log::warning('Percobaan memulihkan tahun ajaran gagal: tidak ditemukan atau tidak di-trash', [
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
