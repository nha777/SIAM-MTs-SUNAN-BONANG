# Tinjauan Pembekuan Desain Basis Data v1 (Database Freeze Review v1) - SIAM

Dokumen ini merupakan bentuk persetujuan akhir dan pembekuan tata kelola basis data (*Database Design Freeze*) sebelum melangkah ke tahap implementasi fisik migrasi pada **Sprint 1**. Dokumen ini merangkum seluruh keputusan arsitektur yang telah disepakati, mengidentifikasi poin-poin yang memerlukan persetujuan akhir, serta melakukan peninjauan kritis terhadap status pemanfaatan ulang (*reusability*) kredensial `users.email`.

---

## 1. Re-Evaluasi Kritis: `users.email` (Reusable vs. Immutable)

Sebelum membekukan skema, kami melakukan analisis mendalam terhadap trade-off keamanan, fungsionalitas, dan integritas data untuk menentukan status terbaik `users.email` pasca-penghapusan logis (*soft-delete*).

### Opsi A: Reusable (Dapat Digunakan Kembali)
Mengizinkan pendaftaran akun baru menggunakan alamat email yang sama dengan akun yang telah di-soft-delete (melalui skema virtual column `active_email`).
* **Pros**: 
  * Menghormati dinamika kepemilikan email (misal: staf sekolah keluar dan email dinasnya diberikan kepada staf pengganti baru).
  * Menghindari penguncian permanen pendaftaran oleh data sampah (*ghost records*).
* **Cons**:
  * **Risiko Kebocoran Data (Security Leak)**: Jika riwayat log audit atau rekam transaksi lampau dicari menggunakan email murni (bukan surrogate `user_id`), data akun lama dapat tersamar oleh aktivitas pengguna baru.
  * **Conflict on Restore**: Jika akun lama yang di-soft-delete ingin dipulihkan (*restored*), operasi pemulihan akan gagal (*throw database error*) karena terjadi tabrakan indeks unik dengan akun baru yang sudah aktif menggunakan email tersebut.

### Opsi B: Immutable (Abadi & Tidak Dapat Digunakan Kembali)
Email yang telah masuk ke dalam sistem selamanya terikat pada baris pengguna (`users.id`) tersebut. Sekalipun akun di-soft-delete, email tersebut tidak dapat digunakan kembali oleh akun lain mana pun.
* **Pros**:
  * **Keamanan Maksimal**: Tidak ada celah di mana pengguna baru dapat memanfaatkan atau diasosiasikan dengan jejak forensik (audit log, histori transaksi) milik pengguna lama.
  * **Pencegahan Fraud**: Mencegah skenario penyamaran identitas di lingkungan sekolah.
  * **Zero Conflict on Restore**: Proses pemulihan akun yang dihapus dijamin selalu berhasil tanpa hambatan keunikan.
* **Cons**:
  * Mengunci alamat email tersebut selamanya di database madrasah. Jika pemilik email ingin kembali membuat akun bersih di masa depan, admin harus melakukan pembersihan data manual (*hard-delete*) atau merubah email akun lama terlebih dahulu.

### **Keputusan Akhir Pembekuan (Final Frozen Decision)**:
**`users.email` ditetapkan sebagai IMMUTABLE.**

#### Alasan Keamanan & Forensik Finansial:
1. **Perlindungan Jejak Transaksi**: Karena SIAM adalah sistem keuangan yang mencatat pembayaran SPP dan invoice, email pengguna sering kali dijadikan acuan sekunder pengiriman tanda terima (e-receipt). Menjamin keunikan email secara mutlak sepanjang waktu mencegah tumpang tindih verifikasi penerima dana.
2. **Kesesuaian dengan Otoritas Terpusat**: Di sekolah madrasah, alamat email bersifat representatif personal. Jika terjadi sengketa penagihan, riwayat login dan perubahan data yang tercatat di audit log harus merujuk secara steril pada entitas fisik yang sama.
3. **Mekanisme Koreksi**: Jika terjadi kasus salah input email, administrator dapat mengubah email pengguna yang di-soft-delete terlebih dahulu (misal: merubahnya menjadi `deleted_123_user@email.com`) sebelum melakukan soft-delete, sehingga membebaskan email asli secara manual dan aman.

---

## 2. Daftar Keputusan Basis Data yang Sudah Final (Frozen)

Berikut adalah komponen desain basis data yang telah **dibekukan** dan tidak boleh diubah selama siklus implementasi Sprint 1:

1. **Modul Penggabungan (Unified Student Domain)**:
   * Menyatukan entitas **Student** dan **Guardian** di dalam satu modul fisik (`app/Modules/Student`) untuk mendukung transaksi pembuatan relasi secara atomik, namun tetap mempertahankan pemisahan logis (*Separation of Concerns*) pada tingkat Service, Repository, dan Interface.
2. **Penghapusan Redundansi `academic_year_id` pada Tabel `classes`**:
   * Menghilangkan ketergantungan transitif dengan menghapus kolom `academic_year_id` dari tabel `classes`. Struktur kelas murni merujuk ke `semester_id`, dan informasi tahun ajaran diturunkan secara relasional melalui tabel `semesters`.
3. **Penggunaan Enum untuk Status Siswa (`students.status`)**:
   * Membekukan penggantian kolom boolean `is_active` menjadi ENUM status eksplisit: `'aktif'`, `'lulus'`, `'mutasi'`, `'keluar'`, `'skorsing'` untuk menjaga ketepatan penargetan billing bulanan secara dinamis.
4. **Perubahan Nama Tabel Wali (`guardians`)**:
   * Mengubah nama tabel secara konseptual dari `parents` menjadi `guardians` dengan dukungan kolom fleksibel `guardian_name` dan `guardian_relation` (ENUM) guna mendukung pendaftaran siswa oleh paman, kakek, atau wali asuh asrama.
5. **Acyclic Foreign Key Relationships (Bebas Referensi Sirkular)**:
   * Seluruh relasi database dirancang linier searah. Kebijakan `ON DELETE RESTRICT` dikunci ketat pada hubungan finansial kritis seperti `students -> guardians` dan `classes -> semesters`.

---

## 3. Daftar Keputusan yang Masih Perlu Persetujuan Akhir

Beberapa poin minor di bawah ini diajukan untuk ditinjau oleh tim pengarah sebelum pengerjaan migrasi fisik dimulai:

| No | Keputusan Teknis | Dampak Bisnis / Teknis | Rekomendasi Tim Arsitek |
| :--- | :--- | :--- | :--- |
| **1** | **Presisi Tipe Data Telepon Wali** | Format internasional nomor seluler membutuhkan penyimpanan string murni agar tidak merusak digit awal `+62` atau `08`. | Gunakan tipe `VARCHAR(20)` dengan indeks unik aktif (`active_phone_number`). |
| **2** | **Konvensi Semester ID pada Siswa** | Apakah tabel `students` memerlukan denormalisasi `semester_id` langsung untuk mempercepat query laporan siswa per semester? | **Tidak perlu**. Biarkan data semester siswa disimpulkan melalui perantara `classes.semester_id` demi menjaga 3NF. |
| **3** | **Skenario Lulus Massal (*Graduation Bulk Update*)** | Proses meluluskan satu angkatan siswa kelas 9 akan mengubah status puluhan siswa sekaligus secara serentak. | Wajib menggunakan pembungkus transaksi (`DB::transaction`) dan mencatat satu entri log audit makro bertingkat *Critical*. |

---

## 4. Rincian Teknis Skema yang Dibekukan (Database Freeze v1)

```text
================================================================================
SIAM DATABASE FREEZE METADATA V1
================================================================================
Database Engine : MariaDB 10.4+ / MySQL 8.0+
Framework Core  : Laravel 12.x
Prinsip Utama   : Golden Rule #3 (Database is the Source of Truth)
                  ADR-002 (Data Access Abstraction - Repository Pattern)
================================================================================

Tabel 1: users (Sovereign Auth)
--------------------------------------------------------------------------------
- Primary Key   : id (BIGINT UNSIGNED, AUTO_INCREMENT)
- Unique Key    : email (VARCHAR(150)) -> IMMUTABLE (Indeks Unik Fisik Standar)
- Audit status  : Enabled via HasAuditLogs

Tabel 2: academic_years (Master Periode)
--------------------------------------------------------------------------------
- Primary Key   : id (BIGINT UNSIGNED, AUTO_INCREMENT)
- Columns       : name (VARCHAR(9), YYYY/YYYY), is_active (BOOLEAN, DEFAULT false)
- Unique Key    : active_name (Virtual Generated) -> UNIQUE(active_name)
- Delete Policy : ON DELETE RESTRICT

Tabel 3: semesters (Periode Akademik)
--------------------------------------------------------------------------------
- Primary Key   : id (BIGINT UNSIGNED, AUTO_INCREMENT)
- Foreign Key   : academic_year_id REFERENCES academic_years(id) ON DELETE RESTRICT
- Columns       : semester (ENUM('ganjil', 'genap')), is_active (BOOLEAN)
- Unique Key    : active_semester (Virtual Generated dari acad_year_id + semester)

Tabel 4: classes (Master Kelas)
--------------------------------------------------------------------------------
- Primary Key   : id (BIGINT UNSIGNED, AUTO_INCREMENT)
- Foreign Key   : semester_id REFERENCES semesters(id) ON DELETE RESTRICT
- Columns       : name (VARCHAR(50)), grade (TINYINT, [7, 8, 9])
- Unique Key    : active_class_name (Virtual Generated dari semester_id + name)

Tabel 5: guardians (Sovereign Profil Wali)
--------------------------------------------------------------------------------
- Primary Key   : id (BIGINT UNSIGNED, AUTO_INCREMENT)
- Foreign Key   : user_id REFERENCES users(id) ON DELETE SET NULL (Nullable)
- Columns       : guardian_name (VARCHAR(150)), guardian_relation (ENUM),
                  phone_number (VARCHAR(20)), address (TEXT)
- Unique Key    : active_phone_number (Virtual Generated) -> UNIQUE(active_phone_number)
- Delete Policy : ON DELETE RESTRICT (Jika dirujuk oleh tabel students aktif)

Tabel 6: students (Sovereign Profil Siswa)
--------------------------------------------------------------------------------
- Primary Key   : id (BIGINT UNSIGNED, AUTO_INCREMENT)
- Foreign Key   : parent_id REFERENCES guardians(id) ON DELETE RESTRICT
                  class_id REFERENCES classes(id) ON DELETE SET NULL (Nullable)
- Columns       : nisn (VARCHAR(10)), name (VARCHAR(150)), gender (ENUM('L', 'P')),
                  birth_place (VARCHAR(100)), birth_date (DATE), status (ENUM)
- Unique Key    : active_nisn (Virtual Generated) -> UNIQUE(active_nisn)
================================================================================
```
