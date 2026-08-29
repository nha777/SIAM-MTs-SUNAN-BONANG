# Sprint 1 Phase A: Database Design Review - SIAM

Dokumen ini mendokumentasikan hasil tinjauan arsitektur basis data (*Database Design Review*) untuk modul akademik dan master data pada Sprint 1. Seluruh rancangan mematuhi prinsip **Database Is The Source of Truth (Golden Rule #3)** dan **ADR-002 (Data Access Abstraction)**.

---

## 1. Diagram Relasi Entitas (ERD - Logical Model)

Berikut adalah representasi hubungan antar-tabel di dalam basis data SIAM:

```text
  +-------------------+
  |   academic_years  |
  +-------------------+
  | PK | id           |
  |    | name         | <---------------+
  |    | is_active    |                 |
  +-------------------+                 |
            |                           |
            | 1                         | 1
            |                           |
            | N                         |
  +-------------------+                 |
  |     semesters     |                 |
  +-------------------+                 |
  | PK | id           |                 |
  | FK | acad_year_id |                 |
  |    | semester     |                 |
  |    | is_active    |                 |
  +-------------------+                 |
            |                           |
            | 1                         |
            |                           |
            | N                         |
  +-------------------+                 |
  |      classes      |                 |
  +-------------------+                 |
  | PK | id           |                 |
  | FK | semester_id  | ----------------+
  |    | name         |
  |    | grade        |
  +-------------------+
            |
            | 1 (Nullable)
            |
            | N
  +-------------------+              +-------------------+
  |     students      |              |       users       |
  +-------------------+              +-------------------+
  | PK | id           |              | PK | id           |
  | FK | parent_id    | <-----+      |    | name         |
  | FK | class_id     | --+   |      |    | email        |
  |    | nisn (UQ)    |   |   |      +-------------------+
  |    | name         |   |   |                |
  |    | status (ENUM)|   |   |                | 1 (Nullable)
  +-------------------+   |   |                |
                          |   |                | 1
                          |   |      +-------------------+
                          |   |      |      parents      |
                          |   |      +-------------------+
                          |   |      | PK | id           |
                          |   +----- | FK | user_id      |
                          |          |    | phone_num(UQ)|
                          |          +-------------------+
                          |
                          +------------------------------------> [ To Financial Modules ]
```

---

## 2. Definisi Skema Tabel, Keys, Index, & Constraint

### A. Tabel `academic_years` (Tahun Ajaran)
Menyimpan periode tahun akademik induk secara menyeluruh.

* **Skema Kolom**:
  * `id` (BIGINT, UNSIGNED, AUTO_INCREMENT, Primary Key)
  * `name` (VARCHAR(9), NOT NULL): Menyimpan format "YYYY/YYYY" (e.g., "2026/2027").
  * `is_active` (BOOLEAN, NOT NULL, DEFAULT `false`)
  * `created_at` (TIMESTAMP)
  * `updated_at` (TIMESTAMP)
  * `deleted_at` (TIMESTAMP, Nullable)
* **Constraints**:
  * `UNIQUE INDEX uq_academic_year_name` (`name`, `deleted_at`): Mencegah duplikasi nama tahun ajaran yang aktif maupun non-aktif, kecuali yang telah di-soft-delete murni.
* **Indexes**:
  * `INDEX idx_academic_years_active` (`is_active`): Mempercepat pencarian tahun ajaran aktif berjalan.
* **Integrity Policy**:
  * Soft Delete: **Enabled** (`deleted_at`).
  * Delete Behavior: **Restrict** (`ON DELETE RESTRICT`) jika dirujuk oleh tabel `semesters`.

---

### B. Tabel `semesters` (Semester)
Menjadi entitas pemisah berpasangan dengan Tahun Ajaran untuk tata kelola data historis per semester.

* **Skema Kolom**:
  * `id` (BIGINT, UNSIGNED, AUTO_INCREMENT, Primary Key)
  * `academic_year_id` (BIGINT, UNSIGNED, NOT NULL)
  * `semester` (ENUM('ganjil', 'genap'), NOT NULL)
  * `is_active` (BOOLEAN, NOT NULL, DEFAULT `false`)
  * `created_at` (TIMESTAMP)
  * `updated_at` (TIMESTAMP)
  * `deleted_at` (TIMESTAMP, Nullable)
* **Foreign Keys**:
  * `fk_semesters_academic_year_id`: Foreign Key ke `academic_years.id`, Cascade Behavior: **Restrict** (`ON DELETE RESTRICT`) untuk menghindari hilangnya data semester jika tahun ajaran dihapus secara tidak sengaja.
* **Constraints**:
  * `UNIQUE INDEX uq_semester_period` (`academic_year_id`, `semester`, `deleted_at`): Menjamin satu tahun ajaran hanya memiliki satu pasang semester ganjil dan genap.
* **Indexes**:
  * `INDEX idx_semesters_active` (`is_active`): Optimalisasi kueri validasi status aktif global.
* **Integrity Policy**:
  * Soft Delete: **Enabled** (`deleted_at`).

---

### C. Tabel `classes` (Kelas)
Wadah fisik pengelompokan siswa berdasarkan tingkatan jenjang madrasah.

* **Skema Kolom**:
  * `id` (BIGINT, UNSIGNED, AUTO_INCREMENT, Primary Key)
  * `academic_year_id` (BIGINT, UNSIGNED, NOT NULL) -- Denormalisasi aman untuk performa filter keuangan lintas tahun ajaran tanpa join semester.
  * `semester_id` (BIGINT, UNSIGNED, NOT NULL)
  * `name` (VARCHAR(50), NOT NULL): Nama kelas (e.g., "Kelas VII-A").
  * `grade` (TINYINT, UNSIGNED, NOT NULL): Tingkat jenjang MTs (`7`, `8`, `9`).
  * `created_at` (TIMESTAMP)
  * `updated_at` (TIMESTAMP)
  * `deleted_at` (TIMESTAMP, Nullable)
* **Foreign Keys**:
  * `fk_classes_academic_year_id`: Foreign Key ke `academic_years.id`, Behavior: **Restrict** (`ON DELETE RESTRICT`).
  * `fk_classes_semester_id`: Foreign Key ke `semesters.id`, Behavior: **Restrict** (`ON DELETE RESTRICT`).
* **Constraints**:
  * `UNIQUE INDEX uq_class_period` (`semester_id`, `name`, `deleted_at`): Mencegah duplikasi nama kelas yang sama dalam satu semester akademik yang berjalan.
* **Indexes**:
  * `INDEX idx_classes_grade` (`grade`): Mempercepat penargetan pembuatan tagihan berdasarkan jenjang sekolah (BR-005).
* **Integrity Policy**:
  * Soft Delete: **Enabled** (`deleted_at`).

---

### D. Tabel `parents` (Orang Tua / Wali)
Menyimpan profil penanggung jawab keuangan utama siswa.

* **Skema Kolom**:
  * `id` (BIGINT, UNSIGNED, AUTO_INCREMENT, Primary Key)
  * `user_id` (BIGINT, UNSIGNED, NULL): ID relasi otentikasi (bisa kosong jika orang tua belum mendaftar akun portal).
  * `father_name` (VARCHAR(150), NOT NULL)
  * `mother_name` (VARCHAR(150), NOT NULL)
  * `phone_number` (VARCHAR(20), NOT NULL)
  * `address` (TEXT, NOT NULL)
  * `created_at` (TIMESTAMP)
  * `updated_at` (TIMESTAMP)
  * `deleted_at` (TIMESTAMP, Nullable)
* **Foreign Keys**:
  * `fk_parents_user_id`: Foreign Key ke `users.id`, Cascade Behavior: **Set Null** (`ON DELETE SET NULL`) untuk memastikan profil orang tua tetap ada di database meskipun akun loginnya dinonaktifkan atau dihapus.
* **Constraints**:
  * `UNIQUE INDEX uq_parents_phone` (`phone_number`, `deleted_at`): Mencegah kekacauan duplikasi nomor WhatsApp untuk gateway notifikasi billing asinkron (BR-011).
* **Integrity Policy**:
  * Soft Delete: **Enabled** (`deleted_at`).

---

### E. Tabel `students` (Siswa)
Entitas sentral madrasah yang mengaitkan seluruh aspek akademik dan finansial.

* **Skema Kolom**:
  * `id` (BIGINT, UNSIGNED, AUTO_INCREMENT, Primary Key)
  * `parent_id` (BIGINT, UNSIGNED, NOT NULL)
  * `class_id` (BIGINT, UNSIGNED, NULL): Nullable jika siswa baru terdaftar dan belum ditempatkan dalam pembagian kelas fisik.
  * `nisn` (VARCHAR(10), NOT NULL)
  * `name` (VARCHAR(150), NOT NULL)
  * `gender` (ENUM('L', 'P'), NOT NULL)
  * `birth_place` (VARCHAR(100), NOT NULL)
  * `birth_date` (DATE, NOT NULL)
  * `status` (ENUM('aktif', 'lulus', 'mutasi', 'keluar', 'skorsing'), NOT NULL, DEFAULT 'aktif')
  * `created_at` (TIMESTAMP)
  * `updated_at` (TIMESTAMP)
  * `deleted_at` (TIMESTAMP, Nullable)
* **Foreign Keys**:
  * `fk_students_parent_id`: Foreign Key ke `parents.id`, Cascade Behavior: **Restrict** (`ON DELETE RESTRICT`) guna menghindari hilangnya data siswa berstatus hutang/tagihan berjalan jika profil orang tua terhapus secara sengaja.
  * `fk_students_class_id`: Foreign Key ke `classes.id`, Cascade Behavior: **Set Null** (`ON DELETE SET NULL`) agar riwayat siswa tetap terjaga meskipun entitas kelas lama dihapus/di-soft-delete pasca-kelulusan.
* **Constraints**:
  * `UNIQUE INDEX uq_students_nisn` (`nisn`, `deleted_at`): Validasi ketat bahwa NISN adalah milik tunggal per individu siswa murni nasional (BR-001).
* **Indexes**:
  * `INDEX idx_students_status` (`status`): Penargetan penarikan invoice rutin bulanan secara efisien (hanya siswa 'aktif').
* **Integrity Policy**:
  * Soft Delete: **Enabled** (`deleted_at`).

---

## 3. Hasil Evaluasi Keputusan Desain (Design Decision Evaluation)

### 3.1 Evaluasi: `is_active` (Boolean) vs `status` (Enum) pada Student

* **Keputusan Awal**: Boolean `is_active` (Aktif / Tidak Aktif).
* **Kelemahan Opsi A**: Kurang ekspresif. Di dalam operasional madrasah riil, ketidakaktifan siswa disebabkan oleh status spesifik (apakah ia sudah lulus, mutasi/pindah sekolah, mengundurkan diri/keluar, atau sedang dalam masa skorsing akademis). Hal ini berimplikasi langsung pada logika keuangan:
  * Siswa berstatus `lulus` atau `mutasi` tidak boleh ditarik tagihan bulanan baru, namun catatan pembayaran lamanya wajib dilestarikan.
  * Siswa berstatus `skorsing` mungkin mendapatkan penangguhan tempo tagihan tanpa menonaktifkan akun seutuhnya.
* **Keputusan Akhir (Hasil Review)**: Menggunakan **`status` (ENUM)** dengan nilai: `'aktif'`, `'lulus'`, `'mutasi'`, `'keluar'`, `'skorsing'`.
  * Status `'aktif'` ekuivalen dengan `is_active = true`.
  * Pendekatan enum ini jauh lebih aman, terdokumentasi, dan meminimalisir technical debt untuk perluasan fitur di fase akademis (Fase 2).

### 3.2 Evaluasi: Pemisahan Semester sebagai Entitas Mandiri

* **Keputusan Awal**: Menyimpan kolom `semester` secara redundan langsung di dalam tabel `academic_years`.
* **Kelemahan Opsi A**: Mengakibatkan redundansi data dan menyulitkan pelacakan transaksi berjalan. Pada pergantian semester ganjil ke genap, bendahara harus mengubah kolom `semester` global pada tahun ajaran yang sama. Ini berisiko merusak integritas pelaporan historis jika ada penargetan tagihan yang merujuk murni pada ID tahun ajaran aktif.
* **Keputusan Akhir (Hasil Review)**: Memisahkan **`semesters` sebagai tabel anak** dari `academic_years`.
  * Satu tahun ajaran melahirkan tepat dua entitas semester (ganjil & genap).
  * Kelas (`classes`) merujuk langsung ke `semester_id`. Ini mempermudah proses kenaikan kelas atau pemindahan kelas siswa di pergantian semester dengan integritas relasional penuh.
  * Hanya ada satu semester yang memiliki status `is_active = true` di seluruh sistem pada satu waktu.
