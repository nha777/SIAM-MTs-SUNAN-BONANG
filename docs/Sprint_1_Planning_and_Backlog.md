# Backlog Hasil Review Sprint 0 & Perencanaan Sprint 1 - SIAM

Dokumen ini berisi tindak lanjut resmi atas keputusan **SPRINT 0 ACCEPTED WITH NOTES**, pengelompokan backlog temuan (W-001 s.d W-005), serta cetak biru perencanaan **Sprint 1** (Modul Akademik & Master Data).

---

## BAGIAN A: Backlog Hasil Review Sprint 0

Berikut adalah klasifikasi temuan teknis dari Sprint 0 berdasarkan Severity, Dampak (*Impact*), Tingkat Kesulitan (*Effort*), serta keselarasan dengan Prinsip Arsitektur (ADR-001 s.d ADR-004).

### 1. Matriks Klasifikasi Temuan (W-001 s.d W-005)

| ID | Keterangan Temuan | Kategori Backlog | Severity | Impact | Effort | Aspek ADR Terkait |
| :--- | :--- | :--- | :---: | :---: | :---: | :--- |
| **W-001** | Validasi status `is_active` dilewatkan saat proses login murni pada controller / auth guard Laravel default. | **1. Must Fix Before Sprint 1** | **Critical** | **High** | Low | **ADR-001** (Sovereignty Domain Auth) & **ADR-002** (Repository) |
| **W-002** | Belum ada mekanisme pembatasan percobaan masuk login (*Rate Limiting*) untuk mencegah serangan *brute force*. | **1. Must Fix Before Sprint 1** | **High** | **High** | Low | **ADR-001** (Aspek Keamanan Keamanan Core) |
| **W-003** | Rujukan ke middleware `auth:sanctum` di file rute API padahal pustaka `laravel/sanctum` belum dipasang di `composer.json`. | **1. Must Fix Before Sprint 1** | **High** | **High** | Low | **ADR-004** (Evolutionary Architecture / Missing Dep) |
| **W-004** | Pencatatan audit log `login_success` masih bersifat sinkronus langsung di dalam `AuthService`, belum asinkron via Event Bus. | **2. Sprint 1 Hardening** | **Medium** | **Medium** | Medium | **ADR-003** (Event-Driven Communication) |
| **W-005** | Pengujian arsitektur (Pest Arch) belum sepenuhnya memindai seluruh komponen dasar (misal: verifikasi BaseRepository murni). | **3. Technical Debt** | **Low** | **Low** | Low | **ADR-001** (Kepatuhan Modular Monolith) |

---

### 2. Deskripsi & Rencana Mitigasi Backlog

#### [W-001] Pengecekan Akun Aktif (`is_active`)
* **Masalah**: Meskipun `UserRepository::findByEmail` memfilter pengguna dengan `is_active = true`, controller auth default atau custom login yang memakai database driver murni dapat melompati filter ini jika tidak divalidasi ketat di level Service pasca-autentikasi.
* **Mitigasi**: Tambahkan pengecekan eksplisit `$user->is_active` segera setelah `Hash::check` di dalam `AuthService::login`. Jika tidak aktif, lemparkan `ValidationException` dengan pesan akun dinonaktifkan.

#### [W-002] Rate Limiting Login
* **Masalah**: Endpoint `POST /login` tidak memiliki pelindung limitasi permintaan, menjadikannya rentan terhadap *credential stuffing* atau *brute force*.
* **Mitigasi**: Terapkan middleware bawaan Laravel `throttle:login` (limit 5 kali percobaan per menit) langsung pada rute login di `Auth/Routes/web.php`.

#### [W-003] Ketiadaan Dependensi Sanctum
* **Masalah**: File `Auth/Routes/api.php` memanggil middleware `auth:sanctum`, tetapi package `laravel/sanctum` tidak dideklarasikan di `composer.json` bawaan.
* **Mitigasi**: Tambahkan `laravel/sanctum` ke dalam dependensi `composer.json` atau ganti rute API default agar menggunakan token bearer manual/session-state murni sesuai kebutuhan monolith sebelum merilis API seluler.

#### [W-004] Penanganan Asinkron Audit Log Login (`login_success`)
* **Masalah**: Melakukan penulisan database audit log secara sinkronus di dalam alur HTTP utama login menambah beban latensi respon pengguna.
* **Mitigasi**: Pindahkan log audit ke domain listener. Picu domain event `Illuminate\Auth\Events\Login` bawaan Laravel, lalu tangkap di `AuditLogListener` untuk disimpan secara asinkron menggunakan Laravel Queue.

#### [W-005] Kelengkapan Pest Arch Tests
* **Masalah**: Aturan arsitektur Pest Arch baru menguji dependensi antar namespace dasar secara makro, namun belum mengunci kepatuhan kelas abstrak secara mikro.
* **Mitigasi**: Tambahkan asersi khusus untuk memastikan seluruh Repositori di masa depan wajib mewarisi `BaseRepository` dan mengimplementasikan Interface terkait.

---

## BAGIAN B: Rencana Perencanaan Sprint 1 (Master Data & Akademik)

* **Durasi Sprint**: 2 Minggu (10 Hari Kerja)
* **Fokus Utama**: Konstruksi Modul Siswa (Student), Orang Tua (Parent), Tahun Ajaran (Academic Year), dan Kelas (Class).
* **Batasan Ketat**: Sesuai instruksi, **DILARANG** menyentuh fitur Finansial, Pembayaran (Payment), Tagihan (Invoice), dan QRIS.

### 1. Sprint Goal
> "Membangun fondasi Master Data akademik madrasah yang andal dan terisolasi secara modular, memungkinkan pengelolaan data siswa, orang tua, kelas, dan tahun ajaran dengan pelacakan perubahan data yang aman (Audit Log) dan otorisasi hak akses (RBAC) yang ketat."

### 2. Batasan Modul (*Module Boundaries*) - ADR-001
Untuk menjaga kesucian arsitektur Modular Monolith, empat domain baru ini diisolasi ke dalam modul-modul berikut:

```text
app/Modules/
├── Student/          # Mengelola data siswa secara mandiri
├── Parent/           # Mengelola profil orang tua/wali murid
└── Academic/         # Menggabungkan subdomain Kelas (Class) & Tahun Ajaran (Academic Year)
```
* **Aturan Lintas Modul**: Modul `Student` dapat berelasi dengan `Parent` melalui foreign key `parent_id` di database, namun manipulasi datanya wajib melalui masing-masing Service Domain. `StudentService` dilarang memanipulasi tabel `parents` secara langsung.

---

### 3. Entitas Basis Data & Relasi (Database Schema)

#### A. Tabel `academic_years` (Tahun Ajaran)
```sql
CREATE TABLE academic_years (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,          -- Contoh: "2026/2027"
    semester ENUM('ganjil', 'genap') NOT NULL,
    is_active BOOLEAN DEFAULT false,    -- Hanya ada 1 tahun ajaran & semester yang aktif serentak
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL            -- Soft deletes (Dev Bible)
);
```

#### B. Tabel `classes` (Kelas)
```sql
CREATE TABLE classes (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    academic_year_id BIGINT NOT NULL,   -- Kunci tamu ke tahun ajaran terkait
    name VARCHAR(50) NOT NULL,          -- Contoh: "Kelas VII-A"
    grade TINYINT NOT NULL,             -- Contoh: 7, 8, 9 (Jenjang MTs)
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE RESTRICT
);
```

#### C. Tabel `parents` (Orang Tua / Wali)
```sql
CREATE TABLE parents (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NULL,                -- Relasi ke tabel users (jika memiliki akun login portal)
    father_name VARCHAR(150) NOT NULL,
    mother_name VARCHAR(150) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,  -- Digunakan untuk verifikasi WA & Notifikasi kelak
    address TEXT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

#### D. Tabel `students` (Siswa)
```sql
CREATE TABLE students (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    parent_id BIGINT NOT NULL,          -- Relasi ke orang tua (Satu orang tua bisa banyak siswa - BR-002)
    class_id BIGINT NULL,               -- Kelas aktif siswa saat ini (bisa NULL jika baru daftar)
    nisn VARCHAR(10) UNIQUE NOT NULL,   -- Nomor Induk Siswa Nasional (10 digit)
    name VARCHAR(150) NOT NULL,
    gender ENUM('L', 'P') NOT NULL,
    birth_place VARCHAR(100) NOT NULL,
    birth_date DATE NOT NULL,
    is_active BOOLEAN DEFAULT true,     -- Status keaktifan siswa di sekolah
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (parent_id) REFERENCES parents(id) ON DELETE RESTRICT,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL
);
```

---

### 4. User Stories & Kriteria Penerimaan (*Acceptance Criteria*)

#### US-001: Pengelolaan Tahun Ajaran (Bendahara/Super Admin)
* **Pernyataan**: Sebagai Bendahara, saya ingin dapat membuat dan mengaktifkan Tahun Ajaran baru agar proses administrasi siswa dan kelas dapat berjalan sesuai periode akademik berjalan.
* **Kriteria Penerimaan (AC)**:
  1. Pengguna dengan hak akses `manage-settings` dapat membuat data tahun ajaran baru.
  2. Hanya diperbolehkan ada **satu** tahun ajaran dengan status `is_active = true` pada satu waktu. Mengaktifkan tahun ajaran baru harus otomatis mengubah status tahun ajaran lama menjadi non-aktif (`is_active = false`) dalam satu transaksi database yang aman.
  3. Setiap pembuatan dan pengaktifan memicu pencatatan audit log (`event: activated_academic_year`) lengkap dengan `request_id`.

#### US-002: Manajemen Kelas (Bendahara)
* **Pernyataan**: Sebagai Bendahara, saya ingin membuat data kelas berdasarkan tahun ajaran aktif agar pengelompokan siswa menjadi lebih teratur.
* **Kriteria Penerimaan (AC)**:
  1. Pembuatan kelas harus memvalidasi kesesuaian `academic_year_id` yang aktif.
  2. Penghapusan kelas bersifat soft-delete dan tidak diperbolehkan jika kelas tersebut masih memiliki siswa aktif terdaftar (`ON DELETE RESTRICT` di level database / validasi service).

#### US-003: Registrasi Data Siswa dan Orang Tua (Bendahara)
* **Pernyataan**: Sebagai Bendahara, saya ingin mendaftarkan data siswa baru beserta profil orang tua mereka agar tercipta hubungan relasi anak-wali murid yang akurat.
* **Kriteria Penerimaan (AC)**:
  1. Bendahara dapat mengaitkan siswa baru ke profil orang tua yang sudah ada di database, atau membuat profil orang tua baru secara bersamaan.
  2. NISN wajib divalidasi unik dan harus terdiri tepat dari 10 digit angka.
  3. Satu orang tua diperbolehkan memiliki lebih dari satu siswa aktif (Mendukung **BR-002**).
  4. Perubahan data siswa (seperti ganti nama, NISN, status keaktifan) wajib dicatat otomatis oleh `HasAuditLogs` trait dengan rincian data lama (`old_values`) dan baru (`new_values`).

---

### 5. Strategi Implementasi Teknis Sprint 1

1. **Test-Driven Foundation**:
   * Sebelum mengimplementasikan controller, buat unit test di `tests/Unit/StudentServiceTest.php` untuk memverifikasi validasi NISN dan relasi parent.
   * Tambahkan arsitektur test di `tests/Architecture/` untuk memastikan modul `Student` tidak mengimpor secara langsung model dari modul `Academic` secara ilegal di luar kontrak interface.
2. **Form Request Validation**:
   * Seluruh parameter masukan untuk CRUD siswa, kelas, wali murid, dan tahun ajaran wajib disaring ketat melalui kelas Form Request (contoh: `StoreStudentRequest`).
3. **Database Transaction**:
   * Pendaftaran siswa baru yang menyertakan data wali murid baru wajib dijalankan di dalam blok `DB::transaction()` pada level `StudentService` untuk menghindari kondisi data yatim (*orphan records*) jika salah satu query gagal.
