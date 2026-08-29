# Laporan Audit Kesiapan Backend - Sprint 2F.1: Academic Class (Rombel/Kelas) - FINAL REVISI ARCHITECTURE

## 1. Tujuan
Melakukan audit menyeluruh terhadap modul Academic Class (Kelas/Rombongan Belajar) di sisi Backend sebelum masuk ke tahap pengembangan UI (Sprint 2F.3). Audit ini mematangkan arsitektur hierarki data SIAM agar sesuai dengan alur operasional madrasah di dunia nyata, mencegah duplikasi data, dan mempersiapkan fondasi yang kuat untuk fitur akademik lanjutan seperti rapor, absensi, dan mutasi siswa.

## 2. Pembaruan Arsitektur & Business Rules (Keputusan Final)
Berdasarkan tinjauan arsitektur, telah disepakati perubahan mendasar yang akan memisahkan konsep ketersediaan kelas dan penempatan siswa:

1. **Perubahan Parent Relasi (Academic Year vs Semester) - APPROVED**: `AcademicClass` harus merujuk ke `academic_year_id` BUKAN `semester_id`. Struktur hierarki yang benar adalah `AcademicYear` memiliki `Semester` dan `AcademicClass`. Kelas/Rombel tetap konstan selama satu tahun ajaran (Ganjil & Genap).
2. **Pemisahan Konsep Entitas Kelas dan Penempatan (Enrollment) - VISION**: Jika terjadi perpindahan rombel di tengah tahun (misal: semester genap), hal ini akan diakomodasi melalui relasi/tabel baru (misal: `student_class_enrollments` yang mengikat `student_id`, `academic_class_id`, dan `semester_id`) di masa depan, bukan dengan menduplikasi `AcademicClass` per semester. Hal ini membuat pencetakan rapor, absensi, dan riwayat siswa lebih dinamis dan akurat.
3. **Kapasitas Rombel (Capacity) - APPROVED**: Menambahkan kolom `capacity`.
4. **Pemisahan Level/Grade dan Nama (Level & Name) - APPROVED**: Menggunakan kolom `grade` (7, 8, 9) dan `name` (A, B, C). Pemformatan nama rombel utuh akan di-*handle* oleh Accessor dengan mapping angka Romawi.
5. **Urutan Tampilan (Display Order) - APPROVED**: Menambahkan kolom `display_order`.
6. **Business Unique Constraint - APPROVED**: Kombinasi `academic_year_id` + `grade` + `name` harus unik untuk mencegah 2 kelas identik di tahun ajaran yang sama.
7. **Wali Kelas (Homeroom Teacher) - DEFERRED**: Ditunda hingga modul Guru siap.
8. **Pembaruan Tabel Academic Year (Side Note) - APPROVED**: Menyarankan penambahan kolom `start_year` (misal 2026) dan `end_year` (misal 2027) pada tabel `academic_years` untuk mempermudah _query_ laporan tanpa *parsing* *string* "2026/2027".

## 3. PASS/FAIL Matrix (Updated)

| Komponen | Status | Keterangan |
|---|---|---|
| **Migration & Schema** | **FAIL** | 1. Foreign Key masih ke `semester_id`, harus diubah ke `academic_year_id`.<br>2. Kolom `capacity` dan `display_order` belum ada.<br>3. Unique constraint harus diubah ke `academic_year_id` + `grade` + `name` (dengan memperhitungkan soft delete). |
| **Model & Relationships** | **FAIL** | 1. Relasi model harus diubah dari `semester()` menjadi `academicYear()`.<br>2. Properti `$fillable` belum mencakup `academic_year_id`, `capacity`, dan `display_order`.<br>3. Accessor nama kelas dengan mapping Romawi belum ada. |
| **Request Validation** | **FAIL** | Form Request perlu disesuaikan: validasi `academic_year_id`, `capacity`, `display_order`, `grade`, dan rule duplikasi unique periodik. |
| **Service & Repository** | **FAIL** | Fitur `restore` (pemulihan soft delete) belum diimplementasikan. |
| **Controller & Routes** | **FAIL** | Controller masih API-only. Endpoint UI (`create`, `edit`, `restore`) beserta route-nya belum ada. |
| **Policy & Permissions** | **FAIL** | Permission `class.restore` belum ditangani. |
| **Audit Trail Compatibility**| **PASS** | Model telah mengimplementasikan trait `HasAuditLogs`. |
| **Soft Delete & Restore** | **FAIL** | *Soft delete* ada pada DB, namun alur `restore` terputus. |
| **Content Negotiation** | **FAIL** | Controller belum memiliki Content Negotiation (`wantsJson()`). |

## 4. Remediation List (Action Items - Sprint 2F.2)

Sebelum masuk ke UI Development (Sprint 2F.3), lakukan remediasi *backend* berikut:

- **[ ] Action 1:** Buat file migration *update* untuk tabel `classes`:
  - Drop *foreign key* dan kolom `semester_id`.
  - Tambahkan kolom `academic_year_id` (*foreign key*).
  - Tambahkan kolom `capacity` (integer, default misal 32).
  - Tambahkan kolom `display_order` (integer, nullable).
  - Drop *virtual column* lama. Buat *virtual column* baru (atau update constraint) gabungan `academic_year_id` + `grade` + `name` (saat `deleted_at IS NULL`) lalu tetapkan sebagai `unique`.
- **[ ] Action 2:** (Opsional/Direkomendasikan) Buat file migration *update* untuk tabel `academic_years` guna menambahkan kolom `start_year` dan `end_year` (integer/year).
- **[ ] Action 3:** Perbarui model `AcademicClass`:
  - Sesuaikan `$fillable` (`academic_year_id`, `grade`, `name`, `capacity`, `display_order`).
  - Ganti relasi menjadi `academicYear()`.
  - Buat mapping grade ke Romawi terpusat dan Accessor `full_name`.
- **[ ] Action 4:** Perbarui Form Request validasi untuk menyesuaikan aturan _business unique_ pada kombinasi rombel dan tahun ajaran, serta penambahan properti baru.
- **[ ] Action 5:** Modifikasi Controller untuk *Content Negotiation* (`wantsJson()`) dan fungsionalitas UI (`create`, `edit`, `restore`).
- **[ ] Action 6:** Implementasikan fitur `restore()` hingga ke layer Service dan Interface.
- **[ ] Action 7:** Tambahkan rute baru di `web.php` dan seeder izin `class.restore`.

## 5. Keputusan Akhir (GO / NO-GO)
**NO-GO** untuk UI Development.
Koreksi *Foreign Key* dari Semester ke Academic Year adalah perubahan arsitektur fundamental yang harus dilakukan sekarang sebelum data siswa masuk. Memisahkan domain eksistensi `AcademicClass` (per tahun) dan penempatan `StudentEnrollment` (per semester) adalah *best practice* dalam Sistem Informasi Sekolah. Sprint 2F.2 akan didedikasikan untuk melakukan perombakan skema dan penyesuaian fungsionalitas *backend* ini.
