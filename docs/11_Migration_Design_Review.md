# Tinjauan Desain Migrasi (Migration Design Review) - SIAM

Dokumen ini mendefinisikan cetak biru penulisan berkas migrasi (*Migration Blueprint*) untuk seluruh tabel inti akademik dan master data pada **Sprint 1** berdasarkan dokumen **Database Freeze v1** yang telah disetujui. 

Rancangan ini memastikan bahwa seluruh tabel dibuat dalam urutan yang aman tanpa melanggar ketergantungan kunci tamu (*foreign key dependencies*), mengonfigurasi batasan integritas data pada tingkat skema (*schema constraints*), serta merinci aturan bisnis (*business rules*) yang wajib dijaga ketat di level Service Layer aplikasi.

---

## 1. Graf Ketergantungan Migrasi (Migration Dependency Graph)

Untuk menghindari kesalahan kegagalan pembuatan tabel (*table creation deadlock*) akibat ketergantungan kunci tamu (*foreign keys*), berkas migrasi wajib dieksekusi dalam urutan linier searah.

Berikut adalah visualisasi graf ketergantungan dari tabel-tabel utama:

```text
    [ users ] (Pra-eksisting)
       |
       | (user_id, Nullable)
       v
  [ guardians ] 
       |
       | (parent_id, RESTRICT)
       v
  [ academic_years ] <--------+
       |                      |
       | (acad_year_id)       | (acad_year_id)
       v                      |
  [ semesters ]               |
       |                      |
       | (semester_id)        |
       v                      |
  [ classes ] ----------------+
       |
       | (class_id, Nullable)
       v
  [ students ]
```

---

## 2. Daftar & Urutan Eksekusi Berkas Migrasi

Berikut adalah daftar nama berkas migrasi beserta stempel waktu teoretis (*logical timestamp*) yang menjamin urutan eksekusi yang benar dari hulu ke hilir:

### Langkah 1: `2014_10_12_000000_create_users_table.php` (Pra-eksisting)
* **Tujuan**: Menyediakan tabel kredensial autentikasi utama sistem (`users`).
* **Sifat**: Induk Teratas (Acyclic Root).

### Langkah 2: `2026_07_16_210001_create_guardians_table.php`
* **Tujuan**: Membuat tabel profil wali murid (`guardians`).
* **Ketergantungan**: `users` (Nullable).

### Langkah 3: `2026_07_16_210002_create_academic_years_table.php`
* **Tujuan**: Membuat tabel induk Tahun Ajaran (`academic_years`).
* **Ketergantungan**: Mandiri (Acyclic Root).

### Langkah 4: `2026_07_16_210003_create_semesters_table.php`
* **Tujuan**: Membuat tabel anak pembagian semester (`semesters`).
* **Ketergantungan**: `academic_years`.

### Langkah 5: `2026_07_16_210004_create_classes_table.php`
* **Tujuan**: Membuat tabel pengelompokan kelas belajar fisik (`classes`).
* **Ketergantungan**: `semesters`.

### Langkah 6: `2026_07_16_210005_create_students_table.php`
* **Tujuan**: Membuat tabel sentral profil siswa (`students`).
* **Ketergantungan**: `guardians` (Wajib/Mandatory) dan `classes` (Opsional/Nullable).

---

## 3. Batasan Integritas Data Tingkat Skema (Database Constraints)

Berikut adalah ringkasan aturan keras (*hard rules*) yang dikunci di level mesin MariaDB/MySQL melalui penulisan skema migrasi:

### 3.1 Konstruksi Kolom Virtual Tergenerasi (*Virtual Generated Columns*)
Untuk mendukung penanganan duplikasi data pada rekam historis yang di-soft-delete, kolom virtual wajib dideklarasikan dengan tipe data pembatas `NULL` yang pas:

1. **`users.active_email`**:
   * *Formula*: `active_email AS (IF(deleted_at IS NULL, email, NULL))`
   * *Constraint*: `UNIQUE INDEX uq_users_active_email (active_email)`
2. **`academic_years.active_name`**:
   * *Formula*: `active_name AS (IF(deleted_at IS NULL, name, NULL))`
   * *Constraint*: `UNIQUE INDEX uq_academic_years_active_name (active_name)`
3. **`guardians.active_phone_number`**:
   * *Formula*: `active_phone_number AS (IF(deleted_at IS NULL, phone_number, NULL))`
   * *Constraint*: `UNIQUE INDEX uq_guardians_active_phone (active_phone_number)`
4. **`students.active_nisn`**:
   * *Formula*: `active_nisn AS (IF(deleted_at IS NULL, nisn, NULL))`
   * *Constraint*: `UNIQUE INDEX uq_students_active_nisn (active_nisn)`

### 3.2 Aturan Hapus Hubungan (*ON DELETE Referential Actions*)
* **`guardians.user_id` $\rightarrow$ `ON DELETE SET NULL`**: Wali murid tetap berhak terdaftar di database keuangan meskipun akun log masuknya dinonaktifkan atau dihapus.
* **`semesters.academic_year_id` $\rightarrow$ `ON DELETE RESTRICT`**: Tahun ajaran induk tidak boleh dihapus jika semester di bawahnya telah terbit.
* **`classes.semester_id` $\rightarrow$ `ON DELETE RESTRICT`**: Semester tidak boleh dihapus jika sudah digunakan untuk melabeli rombongan belajar kelas fisik.
* **`students.parent_id` $\rightarrow$ `ON DELETE RESTRICT`**: Profil wali murid dilarang keras dihapus selama masih dikaitkan dengan siswa aktif (demi integritas tagihan finansial).
* **`students.class_id` $\rightarrow$ `ON DELETE SET NULL`**: Siswa akan otomatis kehilangan ikatan kelas fisik (misal saat lulus atau naik tingkat sebelum penempatan baru) tanpa menghapus rekam medis profil siswa dari database.

---

## 4. Aturan Bisnis Tingkat Aplikasi (Service Layer Business Rules)

Ada beberapa pembatasan fungsional penting yang **tidak dapat dijamin seutuhnya** oleh basis data karena keterbatasan logika SQL transaksional, sehingga wajib dikawal ketat oleh kode program di dalam **Service Layer** aplikasi:

### 4.1 Aturan Atribut "Tunggal Aktif" (Single Active Period - BR-009)
* **Masalah**: Database mengizinkan banyak baris tabel `academic_years` atau `semesters` memiliki kolom `is_active = true`. Database tidak memiliki mekanisme bawaan untuk menonaktifkan baris lain secara asinkron saat baris baru diaktifkan.
* **Solusi Service Layer**: 
  * Di dalam `AcademicYearService::activate(id)`, setiap kali satu periode diaktifkan, jalankan kueri massal untuk merubah seluruh status baris lain menjadi `false` terlebih dahulu di dalam satu transaksi aman:
    ```php
    DB::transaction(function () use ($id) {
        AcademicYear::query()->update(['is_active' => false]);
        AcademicYear::where('id', $id)->update(['is_active' => true]);
    });
    ```

### 4.2 Validasi Atomisitas Registrasi Siswa-Wali (Atomic Registration)
* **Masalah**: Saat bendahara mendaftarkan siswa baru bersama wali murid baru, kesalahan di tengah jalan (misal format NISN salah) dapat mengakibatkan data wali murid terlanjur tersimpan tanpa ada data siswa terkait (*orphan guardian metadata*).
* **Solusi Service Layer**:
  * Pendaftaran siswa dan wali murid yang dilakukan dalam satu formulir masukan wajib dikunci di dalam blok `DB::transaction()` pada `StudentService`. Jika salah satu gagal, seluruh transaksi di-rollback bersih.

### 4.3 Pengecekan Anggota Rombongan Belajar Sebelum Penghapusan Kelas
* **Masalah**: Meskipun kunci asing `students.class_id` diatur `ON DELETE SET NULL`, aturan bisnis melarang penghapusan kelas secara sengaja jika kelas tersebut masih menampung siswa aktif untuk menghindari kondisi "Siswa Tanpa Kelas".
* **Solusi Service Layer**:
  * Di dalam `ClassService::delete(id)`, periksa jumlah siswa aktif terlebih dahulu:
    ```php
    if ($class->students()->where('status', 'aktif')->exists()) {
        throw new ValidationException("Kelas dilarang dihapus karena masih dihuni oleh siswa aktif.");
    }
    ```

### 4.4 Sinkronisasi Soft-Delete Wali dan Siswa (Cascade Deactivation)
* **Masalah**: Saat profil wali murid di-soft delete, siswa-siswa di bawah perwaliannya masih terlihat aktif secara individu di sistem karena tidak ada cascade soft-delete native di tingkat DB MySQL/MariaDB.
* **Solusi Service Layer**:
  * Di dalam `GuardianService::delete(id)`, pemicuan soft-delete wali murid wajib otomatis memicu penonaktifan status siswa anaknya ke `'keluar'` atau `'mutasi'` untuk mencegah kegagalan pembuatan tagihan bulanan berjalan (*phantom billing*).
