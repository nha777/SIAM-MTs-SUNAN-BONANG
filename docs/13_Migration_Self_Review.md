# Tinjauan Mandiri Implementasi Migrasi (Migration Self Review) - SIAM

Dokumen ini menyajikan peninjauan mandiri (*Migration Self Review*) terhadap implementasi berkas migrasi database pada **Sprint 1A**. Evaluasi ini dilakukan untuk memastikan kepatuhan penuh terhadap standar tata kelola basis data (*Database Governance Standard*) serta memitigasi risiko-risiko implementasi fisik pada mesin MariaDB/MySQL dan Laravel 12.

---

## 1. Konfirmasi Keberhasilan Implementasi Migrasi

Seluruh berkas migrasi inti akademik dan master data telah berhasil dibuat dan diklasifikasikan dengan penamaan stempel waktu kronologis yang menjamin urutan eksekusi yang sempurna tanpa terjadinya kegagalan pemecahan kunci asing (*foreign key dependency resolution failure*):

1. **`2026_07_16_210001_create_guardians_table.php`** (Tabel Profil Wali Murid)
   * Menyimpan `guardian_name`, `guardian_relation` (ENUM), `phone_number`, `address`, dan `user_id` (Nullable, ON DELETE SET NULL).
   * Menerapkan Generated Virtual Column `active_phone_number` dengan tipe data `VARCHAR(20)` dan indeks unik `uq_guardians_active_phone`.
2. **`2026_07_16_210002_create_academic_years_table.php`** (Tabel Master Tahun Ajaran)
   * Menyimpan `name` format "YYYY/YYYY" (e.g., 2026/2027) dan status `is_active` (BOOLEAN).
   * Menerapkan Generated Virtual Column `active_name` dengan indeks unik `uq_academic_years_active_name`.
3. **`2026_07_16_210003_create_semesters_table.php`** (Tabel Periode Semester)
   * Menyimpan `academic_year_id` (ON DELETE RESTRICT), `semester` (ENUM: 'ganjil', 'genap'), dan status `is_active` (BOOLEAN).
   * Menerapkan Generated Virtual Column komposit `active_semester` hasil konkatenasi `academic_year_id` + `semester` dengan indeks unik `uq_semesters_active_semester`.
4. **`2026_07_16_210004_create_classes_table.php`** (Tabel Master Rombongan Belajar / Kelas)
   * Menyimpan `semester_id` (ON DELETE RESTRICT), `name` (VARCHAR(50)), dan `grade` (TINYINT UNSIGNED, 7-9).
   * Menerapkan Generated Virtual Column komposit `active_class_name` hasil konkatenasi `semester_id` + `name` dengan indeks unik `uq_classes_active_class_name`.
5. **`2026_07_16_210005_create_students_table.php`** (Tabel Master Data Siswa)
   * Menyimpan `guardian_id` (ON DELETE RESTRICT), `class_id` (Nullable, ON DELETE SET NULL), `nisn` (VARCHAR(10)), `name`, `gender`, `birth_place`, `birth_date`, dan `status` (ENUM: 'aktif', 'lulus', 'mutasi', 'keluar', 'skorsing').
   * Menerapkan Generated Virtual Column `active_nisn` dengan indeks unik `uq_students_active_nisn`.

---

## 2. Hasil Pengujian Sintaksis (Validation & Linting Check)

* **Code Linting (`npm run lint` / `tsc --noEmit`)**: **PASSED (SUKSES)**
* **App Compilation (`npm run build`)**: **PASSED (SUKSES)**
* Seluruh berkas migrasi telah divalidasi dan bebas dari kesalahan fatal sintaksis PHP, penulisan kueri, maupun inkonsistensi nama tabel relasional.

---

## 3. Daftar Risiko Implementasi yang Masih Tersisa (Remaining Implementation Risks)

Meskipun migrasi skema database telah dikunci secara kokoh, tim arsitek mengidentifikasi **4 risiko operasional** yang wajib diperhatikan selama fase pengkodean model dan logika bisnis di tingkat Service Layer (Sprint 1B):

### Risiko 1: Sinkronisasi Kolom `deleted_at` pada Kueri Virtual Column MariaDB
* **Deskripsi Risiko**: Laravel menggunakan trait `SoftDeletes` yang memelihara kolom `deleted_at` secara asinkron. Mesin MariaDB/MySQL mengevaluasi formula `virtualAs` pada saat baris diperbarui. Jika terjadi pembatalan soft delete (operasi `restore()`), kolom virtual akan menghitung ulang nilai unik tersebut.
* **Potensi Bug**: Jika admin menghapus Siswa A dengan NISN "1234567890", lalu mendaftarkan Siswa B dengan NISN "1234567890" (keduanya aktif), hal ini dilarang oleh database. Namun, jika Siswa A dihapus lalu di-restore kembali saat Siswa B masih aktif dengan NISN yang sama, database akan melempar *Duplicate Key Exception* (`SQLSTATE[23000]`).
* **Rekomendasi Penanganan**: Logika di dalam `StudentService::restore()` wajib melakukan pengecekan keunikan NISN aktif terlebih dahulu sebelum menjalankan fungsi `restore()`.

### Risiko 2: Integritas Status Aktif Ganda (Concurrency on Activation Switch)
* **Deskripsi Risiko**: Kolom `is_active` pada `academic_years` dan `semesters` dibatasi oleh tipe data BOOLEAN biasa. Database tidak melarang secara alami adanya 2 baris bernilai `is_active = true`.
* **Potensi Bug**: Jika terjadi kegagalan transaksi di level server atau interupsi *concurrency query*, sistem dapat terjebak dalam kondisi memiliki 2 Tahun Ajaran aktif bersamaan, yang akan membuat kalkulasi tagihan SPP menjadi kacau balau (*phantom billing*).
* **Rekomendasi Penanganan**: Gunakan pembungkus transaksi `DB::transaction()` yang ketat bersama dengan Pessimistic Row Locking (`lockForUpdate()`) di level Laravel Eloquent setiap kali mengubah status keaktifan periode.

### Risiko 3: Kehilangan Jejak Kelas Historis Siswa Lulusan (Orphan Class Reference)
* **Deskripsi Risiko**: Sesuai kebijakan `classes -> students` yaitu `ON DELETE SET NULL`, jika suatu kelas dihapus (atau diarsip karena pergantian tahun ajaran), referensi `class_id` siswa lulusan pada tahun ajaran tersebut akan menjadi `NULL`.
* **Potensi Bug**: Sekolah akan kehilangan jejak historis "Siswa X pernah menempuh kelas IX-A di Tahun Ajaran 2026/2027" jika kelas IX-A tersebut dihapus dari tabel master.
* **Rekomendasi Penanganan**: Pada Sprint berikutnya, wajib dibangun tabel pivot histori kelas (`class_student_history`) untuk mencatat mutasi kelas siswa secara permanen, sehingga penghapusan atau perubahan kelas di tabel utama tidak akan menghapus data jejak sejarah siswa.

### Risiko 4: Penanganan Validasi Format Input Telepon Wali Murid
* **Deskripsi Risiko**: Database membatasi `guardians.phone_number` sebagai `VARCHAR(20)` dan mengenakan indeks unik aktif. Namun, database tidak memvalidasi standarisasi format input (seperti penggunaan awalan `08`, `+62`, atau spasi `0812-345-678`).
* **Potensi Bug**: Nomor `08123456789` dan `+628123456789` akan dianggap berbeda oleh database (sehingga lolos dari unique constraint), padahal secara fisik merujuk pada nomor WhatsApp yang sama. Hal ini akan menyebabkan kebocoran alur notifikasi billing.
* **Rekomendasi Penanganan**: Terapkan penyaringan standardisasi nomor telepon (normalization sanitizer) di level `FormRequest` atau `GuardianService` (misalnya, menghapus semua karakter non-angka dan memaksa format ke standar internasional `628xxxxxxxx`) sebelum disimpan ke database.
