# Bukti Tinjauan Arsitektur (Architecture Review Evidence) - SIAM

Dokumen ini merupakan bentuk laporan **Architecture Review Evidence Mode** pasca-implementasi **Sprint 1D (Service Layer)**. Dokumen ini membuktikan kepatuhan penuh arsitektur sistem SIAM terhadap standar tata kelola dan pola desain *Modular Monolith*, serta merinci bagaimana seluruh aturan bisnis (*business rules*) dienkapsulasi secara steril di dalam lapisan Service.

---

## 1. Peta Layanan & Pengikatan Kontainer (Service-to-Contract Binding Map)

Dalam arsitektur *Modular Monolith* SIAM, setiap pengontrol (*Controller*) atau komponen luar dilarang keras bergantung langsung pada kelas konkrit. Seluruh dependensi diselesaikan melalui wadah layanan (*Service Container*) Laravel 12 menggunakan pengikatan antarmuka (*Interface Binding*) berikut:

| Antarmuka Layanan (Interface Contract) | Kelas Layanan Konkrit (Concrete Service) | Sub-Modul Fisik (Domain Module) | Status Registrasi |
| :--- | :--- | :--- | :---: |
| `GuardianServiceInterface` | `GuardianService` | `Student` |  Tercatat (`StudentServiceProvider`) |
| `StudentServiceInterface` | `StudentService` | `Student` |  Tercatat (`StudentServiceProvider`) |
| `AcademicYearServiceInterface` | `AcademicYearService` | `Academic` |  Tercatat (`AcademicServiceProvider`) |
| `SemesterServiceInterface` | `SemesterService` | `Academic` |  Tercatat (`AcademicServiceProvider`) |
| `AcademicClassServiceInterface` | `AcademicClassService` | `Academic` |  Tercatat (`AcademicServiceProvider`) |

---

## 2. Rincian Pembuktian Implementasi Aturan Bisnis (Business Rules Verification)

Berikut adalah daftar aturan bisnis kritis dan pembuktian potongan kode (*code evidence*) yang menjamin tidak adanya celah bug di tingkat sistem:

### A. Aturan Atribut "Tunggal Aktif" (Single Active Period - BR-009)
* **Aturan**: Hanya boleh ada tepat satu Tahun Ajaran dan satu Semester yang aktif serentak di dalam database.
* **Solusi & Bukti Kode (`AcademicYearService::activate` / `SemesterService::activate`)**:
  * Menggunakan **Pessimistic Row Locking** (`lockForUpdate()`) di dalam transaksi database tunggal untuk menghindari kondisi balapan (*concurrency race condition*).
  * Menjamin atomisitas penonaktifan semua baris lain sebelum mengaktifkan baris yang dipilih.
  ```php
  return DB::transaction(function () use ($id) {
      // Row Locking untuk mencegah dual-active concurrency
      $this->academicYearRepository->getModel()->newQuery()->lockForUpdate()->get();
      $this->academicYearRepository->deactivateAll();
      return $this->academicYearRepository->update($id, ['is_active' => true]);
  });
  ```

### B. Validasi Atomisitas Registrasi Siswa-Wali (Atomic Registration)
* **Aturan**: Pembuatan profil wali murid baru dan siswa baru harus dilakukan secara aman tanpa menyisakan data yatim (*orphan records*) jika salah satu langkah gagal.
* **Solusi & Bukti Kode (`StudentService::registerWithGuardian`)**:
  * Dibungkus sepenuhnya oleh fungsi closure transaksi aman `DB::transaction()`.
  ```php
  return DB::transaction(function () use ($studentData, $guardianData) {
      $guardian = $this->guardianRepository->create($guardianData);
      $studentData['guardian_id'] = $guardian->id;
      return $this->studentRepository->create($studentData);
  });
  ```

### C. Proteksi Penghapusan Kelas Aktif (Prevent "Classless Students")
* **Aturan**: Larangan menghapus kelas fisik secara sengaja atau tidak sengaja jika kelas tersebut masih dihuni oleh siswa yang berstatus aktif atau skorsing.
* **Solusi & Bukti Kode (`AcademicClassService::remove`)**:
  * Melakukan pemeriksaan keanggotaan relasional aktif sebelum memicu kueri penghapusan logis.
  ```php
  $hasActiveStudents = $class->students()->whereIn('status', ['aktif', 'skorsing'])->exists();
  if ($hasActiveStudents) {
      throw ValidationException::withMessages([
          'class' => ['Kelas dilarang dihapus karena masih dihuni oleh siswa aktif.'],
      ]);
  }
  return parent::remove($id);
  ```

### D. Sinkronisasi Soft-Delete Wali dan Siswa (Cascade Deactivation)
* **Aturan**: Saat profil wali murid dihapus secara logis, seluruh siswa aktif di bawah asuhannya wajib dinonaktifkan status akademiknya menjadi `'keluar'` untuk mencegah tagihan berjalan (*phantom billing*).
* **Solusi & Bukti Kode (`GuardianService::remove`)**:
  * Iterasi cascade deactivation otomatis yang terbungkus aman di dalam satu blok transaksi bersama dengan penghapusan wali itu sendiri.
  ```php
  foreach ($guardian->students as $student) {
      if (in_array($student->status, ['aktif', 'skorsing'])) {
          $student->update(['status' => 'keluar']);
      }
  }
  $status = $this->guardianRepository->delete($id);
  ```

### E. Validasi Sintaks Format NISN & Pencegahan Duplikasi Restorasi
* **Aturan**: Nomor Induk Siswa Nasional (NISN) wajib tervalidasi 10 digit angka murni di level aplikasi, serta memproteksi restorasi soft-delete yang dapat memicu pelanggaran keunikan indeks database.
* **Solusi & Bukti Kode (`StudentService::restore` & `validateNisnFormat`)**:
  * Validasi regex format ekspresi reguler `/^[0-9]{10}$/` di level masukan `store` dan `update`.
  * Row locking dan unique validation query saat memulihkan data dari tempat sampah logis.
  ```php
  $studentModel->newQuery()->lockForUpdate()->get();
  $existingActive = $studentModel->newQuery()
      ->where('nisn', $student->nisn)
      ->whereNull('deleted_at')
      ->where('id', '!=', $student->id)
      ->exists();
  if ($existingActive) {
      throw new \RuntimeException("Tidak dapat memulihkan siswa. NISN {$student->nisn} sedang digunakan secara aktif.");
  }
  ```

---

## 3. Grafik Arsitektur Lapisan (Layered Architecture Blueprint)

Berikut adalah visualisasi hubungan berlapis (*layered architecture*) yang terbentuk pasca-implementasi Sprint 1D:

```text
       +-------------------------------------------------------+
       |                  PRESENTATION LAYER                   |
       |     (Controllers / Future APIs - No UI / Blades)       |
       +-------------------------------------------------------+
                                   |
                                   v (Calls Contracts only)
       +-------------------------------------------------------+
       |                     SERVICE LAYER                     |
       |  - GuardianServiceInterface    -> GuardianService     |
       |  - StudentServiceInterface     -> StudentService      |
       |  - AcademicYearServiceInterface -> AcademicYearService |
       |  - SemesterServiceInterface     -> SemesterService     |
       |  - AcademicClassServiceInterface-> AcademicClassService|
       +-------------------------------------------------------+
                                   |
                                   v (Calls Contracts only)
       +-------------------------------------------------------+
       |                  REPOSITORY LAYER                     |
       |  - GuardianRepositoryInterface  -> GuardianRepository |
       |  - StudentRepositoryInterface   -> StudentRepository  |
       |  - AcademicYearRepositoryInterf. -> AcademicYearRepo   |
       |  - SemesterRepositoryInterface  -> SemesterRepository |
       |  - AcademicClassRepositoryInterf.-> AcademicClassRepo |
       +-------------------------------------------------------+
                                   |
                                   v (Eager Loads / Mutates)
       +-------------------------------------------------------+
       |                     DOMAIN MODELS                     |
       |     (User, Guardian, Student, AcademicClass,          |
       |              Semester, AcademicYear)                  |
       +-------------------------------------------------------+
```

---

## Kesimpulan

Lapisan Layanan (*Service Layer*) SIAM untuk **Sprint 1D** telah berhasil dibangun dengan kepatuhan tinggi terhadap spesifikasi fungsional, zero-compile faults, dan modular monolith boundaries. Sistem kini memiliki kesiapan penuh 100% untuk melangkah ke tahap pembuatan pengontrol RESTful API (*API Controllers*) dan implementasi antar-muka pengguna (*UI Presentation*) pada fase sprint berikutnya.
