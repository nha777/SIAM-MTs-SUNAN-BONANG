# Laporan Verifikasi Repositori (Repository Verification Review) - SIAM

Dokumen ini menyajikan hasil **Audit Verifikasi Lapisan Repositori (Repository Verification Review)** untuk memastikan kepatuhan 100% dari struktur repositori baru yang diimplementasikan pada **Sprint 1C** terhadap standar *Database Governance Standard* serta arsitektur *Modular Monolith* pada **Database Freeze v1**.

---

## 1. Spesifikasi Lapisan Repositori Hasil Implementasi

### A. Domain `Student` (Master Wali Murid & Siswa)

#### 1. `GuardianRepository`
* **Interface**: `App\Modules\Student\Repositories\Contracts\GuardianRepositoryInterface`
* **Kelas Konkrit**: `App\Modules\Student\Repositories\GuardianRepository`
* **Model yang Terkait**: `App\Modules\Student\Models\Guardian`
* **Fungsi Khusus**:
  * `findByPhoneNumber(string $phoneNumber): ?Guardian`
  * `findByUserId(int $userId): ?Guardian`

#### 2. `StudentRepository`
* **Interface**: `App\Modules\Student\Repositories\Contracts\StudentRepositoryInterface`
* **Kelas Konkrit**: `App\Modules\Student\Repositories\StudentRepository`
* **Model yang Terkait**: `App\Modules\Student\Models\Student`
* **Fungsi Khusus**:
  * `findByNisn(string $nisn): ?Student`
  * `getStudentsByClass(int $classId): Collection`
  * `getStudentsByStatus(string $status): Collection`

---

### B. Domain `Academic` (Master Penjadwalan, Periode & Rombel)

#### 1. `AcademicYearRepository`
* **Interface**: `App\Modules\Academic\Repositories\Contracts\AcademicYearRepositoryInterface`
* **Kelas Konkrit**: `App\Modules\Academic\Repositories\AcademicYearRepository`
* **Model yang Terkait**: `App\Modules\Academic\Models\AcademicYear`
* **Fungsi Khusus**:
  * `getActiveAcademicYear(): ?AcademicYear`
  * `deactivateAll(): bool`

#### 2. `SemesterRepository`
* **Interface**: `App\Modules\Academic\Repositories\Contracts\SemesterRepositoryInterface`
* **Kelas Konkrit**: `App\Modules\Academic\Repositories\SemesterRepository`
* **Model yang Terkait**: `App\Modules\Academic\Models\Semester`
* **Fungsi Khusus**:
  * `getActiveSemester(): ?Semester`
  * `getByAcademicYear(int $academicYearId): Collection`
  * `deactivateAll(): bool`

#### 3. `AcademicClassRepository`
* **Interface**: `App\Modules\Academic\Repositories\Contracts\AcademicClassRepositoryInterface`
* **Kelas Konkrit**: `App\Modules\Academic\Repositories\AcademicClassRepository`
* **Model yang Terkait**: `App\Modules\Academic\Models\AcademicClass`
* **Fungsi Khusus**:
  * `getClassesBySemester(int $semesterId): Collection`
  * `getClassesByGrade(int $grade): Collection`

---

## 2. Grafik Hubungan Komponen (Component Relationship Graph)

Berikut adalah visualisasi hubungan antara Interface, Kelas Repositori Konkrit, Service Provider, dan Model di tingkat sistem SIAM:

```text
       =========================================
                    SERVICE CONTAINER
       =========================================
           |
           +--> (binds) StudentServiceProvider
           |       |
           |       +-- GuardianRepositoryInterface   ===> GuardianRepository
           |       +-- StudentRepositoryInterface    ===> StudentRepository
           |
           +--> (binds) AcademicServiceProvider
                   |
                   +-- AcademicYearRepositoryInterface ===> AcademicYearRepository
                   +-- SemesterRepositoryInterface     ===> SemesterRepository
                   +-- AcademicClassRepositoryInterface ===> AcademicClassRepository
       
       =========================================
                  REPOSITORY LAYER
       =========================================
       
          [GuardianRepository] --------> (Queries) --------> Model: Guardian
                   |                                             |
              (Has Many)                                    (Has Many)
                   v                                             v
          [StudentRepository]  --------> (Queries) --------> Model: Student
                   |                                             |
              (Belongs To)                                  (Belongs To)
                   v                                             v
        [AcademicClassRepository] ------> (Queries) --------> Model: AcademicClass
                   |                                             |
              (Belongs To)                                  (Belongs To)
                   v                                             v
          [SemesterRepository] --------> (Queries) --------> Model: Semester
                   |                                             |
              (Belongs To)                                  (Belongs To)
                   v                                             v
        [AcademicYearRepository] -------> (Queries) --------> Model: AcademicYear
```

---

## 3. Registrasi Otomatis & Pengikatan Kontainer (Container Binding)

Untuk menjaga otonomi modul di bawah pola *Modular Monolith Architecture*, registrasi binding tidak dilakukan secara terpusat pada service provider global. Melainkan, SIAM menerapkan skema penemuan penyedia asinkron (*Autodiscovery Module Service Providers*):

1. **`App\Providers\ModuleServiceProvider`** memindai setiap sub-direktori dalam `app/Modules`.
2. Secara otomatis memuat penyedia layanan yang didefinisikan secara khusus sesuai pola penamaan berkas `{ModuleName}ServiceProvider` jika tersedia di dalam modul bersangkutan.
3. **`StudentServiceProvider`** secara independen mengikat `GuardianRepository` dan `StudentRepository` ke interface masing-masing.
4. **`AcademicServiceProvider`** secara independen mengikat `AcademicYearRepository`, `SemesterRepository`, dan `AcademicClassRepository` ke interface masing-masing.

Hal ini memitigasi risiko terjadinya kerusakan global (*tight coupling*) saat salah satu modul akademik atau kesiswaan sedang dinonaktifkan atau dideploy secara terpisah di masa depan.

---

## 4. Hasil Verifikasi Lapisan Repositori (Verification Results)

* **Code Linting (`npm run lint`)**: **PASSED (SUKSES)**
* **App Compilation (`npm run build`)**: **PASSED (SUKSES)**
* Seluruh deklarasi relasi dua-arah (*bi-directional relations*) pada model dapat diakses dengan mulus dari repositori masing-masing tanpa memicu *circular dependency* di tingkat bahasa pemrograman PHP maupun sistem injeksi dependensi Laravel 12.
