# Laporan Risiko Pra-Implementasi Sprint 1 (Pre-Implementation Review) - SIAM

Laporan audit ini menyajikan analisis risiko pra-implementasi (*Pre-Implementation Review*) yang komprehensif sebelum fase penulisan kode (*coding*) dan migrasi basis data Sprint 1 dimulai. Tujuan utama laporan ini adalah mendeteksi dini celah keamanan, inkonsistensi penamaan, potensi kegagalan migrasi di mesin MariaDB, serta ambiguitas aturan bisnis (*business rules*) guna memastikan zero-fault execution pada Sprint 1.

---

## 1. Identifikasi Konflik & Ambiguitas Aturan Bisnis (Business Rules Ambiguity)

### A. Kontradiksi Keaktifan Siswa dan Orang Tua / Wali (Deactivation Cascade Conflict)
* **Temuan**: Sesuai kesepakatan, menonaktifkan wali murid (`guardians`) wajib menonaktifkan seluruh siswa di bawah perwaliannya. Namun, ada ambiguitas jika sebaliknya terjadi:
  * Jika salah satu siswa di-soft delete atau dinonaktifkan statusnya menjadi `'keluar'` (misal dari 3 bersaudara yang bersekolah di madrasah yang sama), apakah profil `guardian` harus ikut dinonaktifkan?
* **Keputusan Resolusi**: **Tidak.** Profil `guardians` harus tetap aktif selama masih memiliki minimal **satu** siswa aktif yang berada di bawah perwaliannya. Profil wali murid hanya boleh di-soft-delete jika seluruh siswa yang dikaitkan dengannya telah berstatus non-aktif (`'lulus'`, `'mutasi'`, atau `'keluar'`).

### B. Transisi Kenaikan Kelas vs Kelulusan (The Graduation Gap)
* **Temuan**: Saat siswa dinyatakan `'lulus'`, status mereka berubah di tabel `students.status = 'lulus'`. Namun, relasi `class_id` mereka saat ini masih menunjuk ke kelas terakhir (misal: "Kelas IX-A").
  * Jika kelas "Kelas IX-A" tersebut di-soft-delete pada tahun ajaran berikutnya karena rombel baru dibuat, kunci asing `students.class_id` diatur `ON DELETE SET NULL`, yang akan menghapus jejak sejarah kelas lulusan siswa tersebut.
* **Keputusan Resolusi**: Di dalam database, biarkan nilai `class_id` menjadi `NULL` saat kelas dihapus. Namun, untuk menjaga sejarah keanggotaan kelas, sistem membutuhkan tabel penengah histori bernama `class_student_history` (diimplementasikan pada fase berikutnya). Untuk Sprint 1, disepakati bahwa profil siswa lulusan yang kelasnya dihapus akan memiliki `class_id = NULL`, tetapi status akademik `'lulus'` tetap terkunci aman.

---

## 2. Analisis Hambatan Integritas Referensial (Referential Integrity Bottlenecks)

### A. Siklus Transaksi Pendaftaran Siswa Baru (Transactional Race Condition)
* **Masalah**: Pendaftaran siswa baru yang menyertakan pembuatan profil wali murid baru dijalankan melalui endpoint tunggal.
  * SQL membutuhkan `guardian_id` terlebih dahulu sebelum menginput baris `students`.
  * Jika pembuatan wali murid berhasil tetapi pembuatan siswa gagal (misal karena NISN duplikat atau server mati di tengah proses), database akan menanggung record wali murid yatim (*orphan guardian*) tanpa siswa yang valid.
* **Mitigasi Risiko**: **Wajib mengunci transaksi penuh di tingkat database**. Kode program Laravel di level Service Layer harus membungkus proses pendaftaran dengan closure `DB::transaction()` dan menerapkan penanganan error terpusat yang melempar `Rollback` jika terjadi anomali masukan.

### B. Penghapusan Kelas Bermasalah (The CASCADE vs RESTRICT Trap)
* **Masalah**: Kebijakan referensi `students.class_id` diatur `ON DELETE SET NULL`. Ini berarti jika admin tidak sengaja menghapus kelas aktif, semua siswa di kelas tersebut akan seketika kehilangan rujukan kelas tanpa peringatan error dari database.
* **Mitigasi Risiko**: Mencegah penghapusan di level aplikasi. Sebelum memicu query delete kelas, `ClassService` wajib memeriksa apakah ada siswa aktif yang mendiami kelas tersebut. Jika ada, proses delete langsung ditolak dengan pesan kesalahan ramah pengguna.

---

## 3. Identifikasi Risiko Aturan Service-Layer (Service-Layer Implementation Risks)

### A. Sinkronisasi Status "Tunggal Aktif" (Single-Active Concurrency Risk)
* **Masalah**: Aturan **BR-009** menyatakan hanya boleh ada satu Tahun Ajaran dan Semester yang aktif serentak.
  * Jika ada dua proses administrator yang mengaktifkan dua Tahun Ajaran berbeda secara bersamaan (*concurrency request*), ada risiko balapan (*race condition*) di mana sistem berakhir dengan dua tahun ajaran aktif sekaligus.
* **Mitigasi Risiko**: Terapkan penguncian baris database (*Row Locking* atau Pessimistic Locking) di Laravel saat melakukan aktivasi:
  ```php
  DB::transaction(function () use ($id) {
      // Kunci baris untuk menghindari pembacaan ganda
      AcademicYear::lockForUpdate()->get(); 
      AcademicYear::query()->update(['is_active' => false]);
      AcademicYear::where('id', $id)->update(['is_active' => true]);
  });
  ```

### B. Validasi Sintaks Format NISN di Luar Database
* **Masalah**: Database mendefinisikan `nisn` sebagai `VARCHAR(10)`. Namun, database tidak memvalidasi apakah string tersebut berisi huruf atau angka murni.
* **Mitigasi Risiko**: Validasi regex wajib dikunci di level `FormRequest` Laravel:
  ```php
  'nisn' => ['required', 'string', 'size:10', 'regex:/^[0-9]{10}$/']
  ```

---

## 4. Inkonsistensi Penamaan & Resolusi Istilah (Naming Inconsistency Audit)

Sebelum migrasi ditulis, seluruh tim menyepakati penyelarasan istilah agar tidak terjadi kebingungan di masa depan:

| Istilah Lama / Alternatif | Istilah Baru yang Disepakati (Frozen) | Ruang Lingkup Tabel / Kolom | Alasan Perubahan |
| :--- | :--- | :--- | :--- |
| `parents` | **`guardians`** | Nama Tabel Utama | Mengakomodasi wali asuh non-orang-tua (BR-012). |
| `parent_id` | **`guardian_id`** | Kolom Foreign Key di `students` | Penyesuaian relasional nama tabel baru. |
| `is_active` (Siswa) | **`status` (ENUM)** | Atribut Keaktifan Siswa | Lebih ekspresif untuk melacak lulus, mutasi, dan keluar. |
| `is_active` (Periode) | **`is_active` (BOOLEAN)** | Atribut Periode Tahun Ajaran & Semester | Cukup boolean karena status murni hanya Aktif / Non-aktif. |

---

## 5. Potensi Kegagalan Migrasi pada MariaDB (Migration Failure Modes)

### A. Kegagalan Sintaks Kolom Virtual Tergenerasi
* **Masalah**: Penulisan fungsi `IF` atau `CASE` di dalam definisi `virtualAs` Laravel dapat menghasilkan sintaks SQL yang tidak valid jika tipe database target adalah MariaDB versi lama.
* **Solusi**: Pastikan penggunaan fungsi standar yang kompatibel universal dengan MariaDB/MySQL. Kode Blueprint Laravel 12 wajib menggunakan ekspresi SQL murni yang kokoh:
  ```php
  $table->string('active_nisn')
        ->virtualAs("CASE WHEN deleted_at IS NULL THEN nisn ELSE NULL END")
        ->nullable();
  ```
  *Penggunaan ekspresi `CASE WHEN` lebih andal dan kompatibel di lintas mesin SQL dibanding fungsi `IF()`.*

### B. Urutan Penghapusan Migrasi (*Migration Down Sequence*)
* **Masalah**: Saat melakukan rollback migrasi (`php artisan migrate:rollback`), kegagalan drop tabel sering terjadi karena tabel induk dihapus sebelum tabel anak yang memiliki foreign key dilepas.
* **Solusi**: Susun urutan rollback (*Down Method*) secara terbalik dari urutan pembuatan:
  1. Drop `students`
  2. Drop `classes`
  3. Drop `semesters`
  4. Drop `academic_years`
  5. Drop `guardians`

---

## Kesimpulan & Kesiapan Coding

Rancangan arsitektur basis data dan migrasi SIAM untuk Sprint 1 dinyatakan **SANGAT MATANG** dan **SIAP UNTUK DIIMPLEMENTASIKAN**. Seluruh potensi risiko teknis telah diidentifikasi beserta rencana mitigasinya di dokumen ini. Langkah selanjutnya adalah memulai pembuatan berkas migrasi fisik dan dilanjutkan dengan konstruksi Service & Repository Layer untuk Modul Akademik.
