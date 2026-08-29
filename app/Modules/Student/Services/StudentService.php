<?php

namespace App\Modules\Student\Services;

use App\Modules\Base\Services\BaseService;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Repositories\Contracts\StudentRepositoryInterface;
use App\Modules\Student\Repositories\Contracts\GuardianRepositoryInterface;
use App\Modules\Student\Services\Contracts\StudentServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentService extends BaseService implements StudentServiceInterface
{
    /**
     * StudentRepository instance.
     */
    protected StudentRepositoryInterface $studentRepository;

    /**
     * GuardianRepository instance.
     */
    protected GuardianRepositoryInterface $guardianRepository;

    /**
     * StudentService constructor.
     */
    public function __construct(
        StudentRepositoryInterface $studentRepository,
        GuardianRepositoryInterface $guardianRepository
    ) {
        parent::__construct($studentRepository);
        $this->studentRepository = $studentRepository;
        $this->guardianRepository = $guardianRepository;
    }

    /**
     * Menyimpan data siswa baru (overrides base store untuk validasi format NISN).
     */
    public function store(array $data): Model
    {
        $this->validateNisnFormat($data['nisn'] ?? null);

        return parent::store($data);
    }

    /**
     * Memperbarui data siswa (overrides base update untuk validasi format NISN jika diubah).
     */
    public function update(int|string $id, array $data): bool
    {
        if (isset($data['nisn'])) {
            $this->validateNisnFormat($data['nisn']);
        }

        return parent::update($id, $data);
    }

    /**
     * Mendaftarkan siswa baru beserta wali muridnya dalam satu transaksi atomik.
     * Mengunci registrasi di tingkat DB untuk menghindari record yatim (orphan guardians).
     */
    public function registerWithGuardian(array $studentData, array $guardianData): Student
    {
        $this->validateNisnFormat($studentData['nisn'] ?? null);

        DB::beginTransaction();
        try {
            // 1. Simpan wali murid terlebih dahulu
            $guardian = $this->guardianRepository->create($guardianData);

            // 2. Set foreign key guardian_id ke dalam data siswa
            $studentData['guardian_id'] = $guardian->id;

            // 3. Simpan data siswa baru
            /** @var Student $student */
            $student = $this->studentRepository->create($studentData);

            Log::info("Registrasi Siswa dan Wali Murid berhasil dilakukan secara atomik", [
                'student_id' => $student->id,
                'guardian_id' => $guardian->id,
                'nisn' => $student->nisn
            ]);

            DB::commit();
            return $student;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Memulihkan siswa dari soft-delete setelah memvalidasi keunikan NISN aktif.
     */
    public function restore(int|string $id): bool
    {
        DB::beginTransaction();
        try {
            // Ambil data siswa dengan trashed (deleted_at is not null) menggunakan model query
            $studentModel = $this->studentRepository->getModel();
            $student = $studentModel->withTrashed()->find($id);

            if (!$student) {
                Log::warning('Percobaan memulihkan siswa gagal: siswa tidak ditemukan', [
                    'id' => $id
                ]);
                DB::rollBack();
                return false;
            }

            // Kunci baris tabel secara spesifik hanya pada record aktif yang memiliki NISN yang sama
            $studentModel->newQuery()
                ->where('nisn', $student->nisn)
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->get();

            // Cek apakah NISN dari siswa ini sedang digunakan secara aktif oleh siswa lain
            $existingActive = $studentModel->newQuery()
                ->where('nisn', $student->nisn)
                ->whereNull('deleted_at')
                ->where('id', '!=', $student->id)
                ->exists();

            if ($existingActive) {
                Log::error('Restorasi siswa dibatalkan karena duplikasi NISN aktif', [
                    'student_id' => $id,
                    'nisn' => $student->nisn
                ]);
                throw new \RuntimeException("Tidak dapat memulihkan siswa. NISN {$student->nisn} sedang digunakan secara aktif oleh siswa lain.");
            }

            $restored = $student->restore();

            Log::info("Siswa berhasil dipulihkan dari soft-delete", [
                'student_id' => $student->id,
                'nisn' => $student->nisn
            ]);

            DB::commit();
            return $restored;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Validasi format NISN (harus berisi tepat 10 digit angka).
     */
    protected function validateNisnFormat(?string $nisn): void
    {
        if (empty($nisn)) {
            return; // allow null/empty NISN for MVP workflows
        }

        if (!preg_match('/^[0-9]{10}$/', $nisn)) {
            throw new \InvalidArgumentException('Format NISN tidak valid. Harus berisi tepat 10 digit angka murni.');
        }
    }
}
