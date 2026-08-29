# Laporan Audit Verifikasi Migrasi (Migration Verification Review) - SIAM

Dokumen ini menyajikan hasil **Audit Verifikasi Migrasi (Migration Verification Review)** untuk memastikan bahwa seluruh berkas migrasi fisik yang telah diimplementasikan pada **Sprint 1A** sesuai 100% dengan spesifikasi yang telah dibekukan pada dokumen **Database Freeze v1**.

---

## 1. Rekapitulasi Skema Tabel Hasil Implementasi

Berikut adalah struktur skema fisik dari 5 tabel utama yang telah dideklarasikan di dalam folder `database/migrations/`:

### A. Tabel `guardians`
* **File**: `2026_07_16_210001_create_guardians_table.php`
* **Kolom Fisik**:
  * `id` (BIGINT UNSIGNED, AUTO_INCREMENT, Primary Key)
  * `user_id` (BIGINT UNSIGNED, NULL)
  * `guardian_name` (VARCHAR(150))
  * `guardian_relation` (ENUM('ayah', 'ibu', 'paman_bibi', 'kakek_nenek', 'lainnya'), DEFAULT 'ayah')
  * `phone_number` (VARCHAR(20))
  * `address` (TEXT)
  * `created_at` (TIMESTAMP, Nullable)
  * `updated_at` (TIMESTAMP, Nullable)
  * `deleted_at` (TIMESTAMP, Nullable)

### B. Tabel `academic_years`
* **File**: `2026_07_16_210002_create_academic_years_table.php`
* **Kolom Fisik**:
  * `id` (BIGINT UNSIGNED, AUTO_INCREMENT, Primary Key)
  * `name` (VARCHAR(9)) -- Format YYYY/YYYY (e.g. 2026/2027)
  * `is_active` (BOOLEAN, DEFAULT false)
  * `created_at` (TIMESTAMP, Nullable)
  * `updated_at` (TIMESTAMP, Nullable)
  * `deleted_at` (TIMESTAMP, Nullable)

### C. Tabel `semesters`
* **File**: `2026_07_16_210003_create_semesters_table.php`
* **Kolom Fisik**:
  * `id` (BIGINT UNSIGNED, AUTO_INCREMENT, Primary Key)
  * `academic_year_id` (BIGINT UNSIGNED)
  * `semester` (ENUM('ganjil', 'genap'))
  * `is_active` (BOOLEAN, DEFAULT false)
  * `created_at` (TIMESTAMP, Nullable)
  * `updated_at` (TIMESTAMP, Nullable)
  * `deleted_at` (TIMESTAMP, Nullable)

### D. Tabel `classes`
* **File**: `2026_07_16_210004_create_classes_table.php`
* **Kolom Fisik**:
  * `id` (BIGINT UNSIGNED, AUTO_INCREMENT, Primary Key)
  * `semester_id` (BIGINT UNSIGNED)
  * `name` (VARCHAR(50)) -- e.g., "Kelas VII-A"
  * `grade` (TINYINT UNSIGNED) -- Rombel jenjang MTs (7, 8, 9)
  * `created_at` (TIMESTAMP, Nullable)
  * `updated_at` (TIMESTAMP, Nullable)
  * `deleted_at` (TIMESTAMP, Nullable)

### E. Tabel `students`
* **File**: `2026_07_16_210005_create_students_table.php`
* **Kolom Fisik**:
  * `id` (BIGINT UNSIGNED, AUTO_INCREMENT, Primary Key)
  * `guardian_id` (BIGINT UNSIGNED)
  * `class_id` (BIGINT UNSIGNED, NULL)
  * `nisn` (VARCHAR(10))
  * `name` (VARCHAR(150))
  * `gender` (ENUM('L', 'P'))
  * `birth_place` (VARCHAR(100))
  * `birth_date` (DATE)
  * `status` (ENUM('aktif', 'lulus', 'mutasi', 'keluar', 'skorsing'), DEFAULT 'aktif')
  * `created_at` (TIMESTAMP, Nullable)
  * `updated_at` (TIMESTAMP, Nullable)
  * `deleted_at` (TIMESTAMP, Nullable)

---

## 2. Definisi Kunci Asing (*Foreign Keys*) & Perilaku Hapus

| Tabel Anak | Kolom Kunci Tamu (FK) | Merujuk Ke | Perilaku Hapus (`ON DELETE`) | Evaluasi Status |
| :--- | :--- | :--- | :--- | :---: |
| `guardians` | `user_id` | `users.id` | **`SET NULL`** |  Sesuai |
| `semesters` | `academic_year_id` | `academic_years.id` | **`RESTRICT`** |  Sesuai |
| `classes` | `semester_id` | `semesters.id` | **`RESTRICT`** |  Sesuai |
| `students` | `guardian_id` | `guardians.id` | **`RESTRICT`** |  Sesuai |
| `students` | `class_id` | `classes.id` | **`SET NULL`** |  Sesuai |

---

## 3. Definisi Generated Virtual Columns & Unique Index

Seluruh tabel master telah dilengkapi dengan kolom virtual tergenerasi (*Generated Virtual Column*) berbasis sintaks `CASE WHEN` yang kompatibel tinggi dengan MariaDB/MySQL guna mengawal keunikan record aktif di bawah trait *Soft Deletes*:

### A. Tabel `guardians`
* **Generated Column**: `active_phone_number` (`VARCHAR(20)`)
  * *Formula*: `CASE WHEN deleted_at IS NULL THEN phone_number ELSE NULL END`
* **Unique Index**: `uq_guardians_active_phone` (`active_phone_number`)

### B. Tabel `academic_years`
* **Generated Column**: `active_name` (`VARCHAR(9)`)
  * *Formula*: `CASE WHEN deleted_at IS NULL THEN name ELSE NULL END`
* **Unique Index**: `uq_academic_years_active_name` (`active_name`)

### C. Tabel `semesters`
* **Generated Column**: `active_semester` (`VARCHAR(50)`)
  * *Formula*: `CASE WHEN deleted_at IS NULL THEN CONCAT(academic_year_id, '-', semester) ELSE NULL END`
* **Unique Index**: `uq_semesters_active_semester` (`active_semester`)

### D. Tabel `classes`
* **Generated Column**: `active_class_name` (`VARCHAR(100)`)
  * *Formula*: `CASE WHEN deleted_at IS NULL THEN CONCAT(semester_id, '-', name) ELSE NULL END`
* **Unique Index**: `uq_classes_active_class_name` (`active_class_name`)

### E. Tabel `students`
* **Generated Column**: `active_nisn` (`VARCHAR(10)`)
  * *Formula*: `CASE WHEN deleted_at IS NULL THEN nisn ELSE NULL END`
* **Unique Index**: `uq_students_active_nisn` (`active_nisn`)

---

## 4. Perbandingan Kesesuaian dengan Database Freeze v1

Tim Audit membandingkan langsung implementasi fisik dengan spesifikasi **Database Freeze v1**:

| Elemen Rancangan | Target Database Freeze v1 | Hasil Implementasi Migrasi | Kesesuaian (Gap Analysis) |
| :--- | :--- | :--- | :---: |
| **Model Wali** | Tabel bernama `guardians` (bukan `parents`) | Tabel bernama `guardians` | **100% Sesuai** |
| **Status Siswa** | ENUM: `'aktif'`, `'lulus'`, `'mutasi'`, `'keluar'`, `'skorsing'` | ENUM: `'aktif'`, `'lulus'`, `'mutasi'`, `'keluar'`, `'skorsing'` | **100% Sesuai** |
| **Semester Link** | `classes.semester_id` tanpa `classes.academic_year_id` | Murni `semester_id`, tidak ada redundansi tahun ajaran | **100% Sesuai** |
| **Keunikan Email** | `users.email` sebagai IMMUTABLE (Indeks Unik Fisik) | Menyesuaikan tabel `users` bawaan untuk validasi login | **100% Sesuai** |
| **Keunikan NISN** | `active_nisn` (Generated Virtual Column) | `active_nisn` (Generated Virtual Column) | **100% Sesuai** |
| **Keunikan Telepon**| `active_phone_number` (Generated Column) | `active_phone_number` (Generated Column) | **100% Sesuai** |

---

## 5. Kesimpulan Audit Verifikasi

Berdasarkan tinjauan di atas, **TIDAK DITEMUKAN ADANYA PENYIMPANGAN ATAU PERBEDAAN** antara kode migrasi yang diimplementasikan dengan cetak biru arsitektur **Database Freeze v1**. Seluruh constraint database, foreign keys, cascade/restrict behaviors, dan generated virtual columns telah dideklarasikan secara presisi dan siap dijalankan dengan aman di lingkungan produksi MariaDB dan Laravel 12.
