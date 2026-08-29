# Desain Domain Master Data - SIAM

Dokumen ini mendefinisikan cetak biru arsitektur domain (*Domain Modelling*) dan desain basis data untuk entitas inti akademik di SIAM: **Student**, **Parent**, **AcademicYear**, **Semester**, dan **Class**.

---

## Analisis Arsitektur: Penempatan Domain "Parent"
Sebelum mengurai entitas, dilakukan analisis apakah domain **Parent** layak menjadi modul mandiri atau digabungkan ke dalam **Student Domain**.

### Opsi A: Modul Terpisah (`app/Modules/Parent`)
* **Kelebihan**: Pemisahan tanggung jawab yang murni; rute portal orang tua dan pengolahan data terisolasi secara fisik.
* **Kekurangan**: Menambah duplikasi boilerplate (*Service*, *Repository*, *Interface*), serta mempersulit penanganan transaksi pendaftaran siswa baru yang membutuhkan pembuatan profil orang tua secara atomik (*atomic transaction*).

### Opsi B: Bagian dari Unified Student Domain (`app/Modules/Student`)
* **Kelebihan**: Transaksi pendaftaran siswa-orang tua berjalan kohesif; meminimalkan overhead pemanggilan lintas-modul pada operasi CRUD dasar; mempermudah join query yang dikelola satu *Aggregate Root*.
* **Kekurangan**: Ukuran direktori modul `Student` menjadi lebih besar.

### **Keputusan Teknis**:
Mengingat prinsip **ADR-004 (Evolutionary Architecture)** yang mengutamakan solusi paling sederhana di awal tanpa melanggar keteraturan, **Parent** akan ditempatkan sebagai **Sovereign Aggregate** di dalam **Unified Student Domain (`app/Modules/Student`)**. 
Secara fisik, mereka berbagi modul yang sama, tetapi secara logis memiliki *Repository*, *Service*, dan *Controller* independen untuk memisahkan siklus hidup data (*Lifecycle Separation*).

---

## 1. Domain: AcademicYear & Semester (Tahun Ajaran)

### 1.1 Purpose
Mengelola kerangka waktu periode akademik berjalan di madrasah untuk keperluan penargetan tagihan dan pengelompokan kelas siswa.

### 1.2 Ownership
* **Module Owner**: `app/Modules/Academic` (Gabungan sub-domain Tahun Ajaran dan Kelas).

### 1.3 Fields (Database Schema)
* `id` (BIGINT, Primary Key, Auto-increment)
* `name` (VARCHAR(50)): Nama tahun ajaran (e.g., "2026/2027").
* `semester` (ENUM('ganjil', 'genap')): Periode semester.
* `is_active` (BOOLEAN): Penentu periode aktif sistem. Default: `false`.
* `created_at` (TIMESTAMP)
* `updated_at` (TIMESTAMP)
* `deleted_at` (TIMESTAMP, Nullable): Dukungan soft-delete.

### 1.4 Validation Rules
* `name`: Wajib diisi, format string maksimal 50 karakter (regex: `^[0-9]{4}\/[0-9]{4}$` untuk memvalidasi format "YYYY/YYYY").
* `semester`: Wajib diisi, nilai harus salah satu dari: `ganjil` atau `genap`.
* `is_active`: Boolean.

### 1.5 Lifecycle
* **Created**: Ditambahkan oleh Super Admin/Bendahara sebelum tahun ajaran baru dimulai (status awal: `inactive`).
* **Activated**: Diaktifkan secara eksplisit. Pengaktifan satu tahun ajaran otomatis menonaktifkan tahun ajaran aktif lainnya.
* **Soft-Deleted**: Hanya bisa dihapus jika tidak memiliki kelas aktif yang merujuk padanya.

### 1.6 Relationships
* `HasMany` ke `classes` (Kelas yang terdaftar pada periode akademik ini).

### 1.7 Business Rules
* **BR-009**: Hanya diperbolehkan ada tepat satu Tahun Ajaran-Semester yang aktif secara bersamaan di dalam sistem.

### 1.8 Audit Requirements
* Perubahan status `is_active` wajib mencatat log audit dengan tingkat keparahan **warning** karena memengaruhi seluruh rujukan transaksi sistem.

---

## 2. Domain: Class (Kelas)

### 2.1 Purpose
Tempat pengelompokan fisik siswa dalam proses belajar mengajar serta menjadi rujukan utama penargetan tagihan berkala.

### 2.2 Ownership
* **Module Owner**: `app/Modules/Academic`.

### 2.3 Fields (Database Schema)
* `id` (BIGINT, Primary Key, Auto-increment)
* `academic_year_id` (BIGINT, Foreign Key): Rujukan ke tahun ajaran.
* `name` (VARCHAR(50)): Nama kelas (e.g., "Kelas VII-A").
* `grade` (TINYINT): Jenjang tingkatan kelas (7, 8, atau 9).
* `created_at` (TIMESTAMP)
* `updated_at` (TIMESTAMP)
* `deleted_at` (TIMESTAMP, Nullable): Dukungan soft-delete.

### 2.4 Validation Rules
* `academic_year_id`: Wajib diisi, harus ada di tabel `academic_years`.
* `name`: Wajib diisi, string maksimal 50 karakter.
* `grade`: Wajib diisi, harus bernilai integer salah satu dari: `7`, `8`, atau `9`.

### 2.5 Lifecycle
* **Created**: Dibuat pada awal tahun ajaran.
* **Soft-Deleted**: Dinonaktifkan menggunakan soft-delete jika periode ajaran selesai. Penghapusan ditolak jika masih ada siswa aktif yang mendiami kelas tersebut.

### 2.6 Relationships
* `BelongsTo` ke `academic_years`.
* `HasMany` ke `students` (Siswa-siswa yang menghuni kelas ini).

### 2.7 Business Rules
* **BR-010**: Kelas tidak boleh dipindahkan ke tahun ajaran lain setelah memiliki siswa aktif terdaftar di dalamnya untuk menjaga konsistensi pelaporan historis tagihan keuangan.

### 2.8 Audit Requirements
* Pencatatan log audit otomatis via trait `HasAuditLogs` untuk setiap pembuatan dan penghapusan kelas.

---

## 3. Domain: Parent (Orang Tua / Wali)

### 3.1 Purpose
Represents penanggung jawab finansial utama dari siswa. Menyimpan kontak utama komunikasi sistem (WhatsApp/SMS) dan akun otentikasi portal orang tua.

### 3.2 Ownership
* **Module Owner**: `app/Modules/Student` (Sebagai Sovereign Aggregate).

### 3.3 Fields (Database Schema)
* `id` (BIGINT, Primary Key, Auto-increment)
* `user_id` (BIGINT, Foreign Key, Nullable): Mengaitkan ke kredensial login portal jika memiliki hak akses.
* `father_name` (VARCHAR(150))
* `mother_name` (VARCHAR(150))
* `phone_number` (VARCHAR(20)): Nomor telepon aktif (format internasional e.g., "+62...").
* `address` (TEXT)
* `created_at` (TIMESTAMP)
* `updated_at` (TIMESTAMP)
* `deleted_at` (TIMESTAMP, Nullable)

### 3.4 Validation Rules
* `father_name` & `mother_name`: Wajib diisi, minimal 3 karakter, maksimal 150 karakter, alfabet dan spasi murni.
* `phone_number`: Wajib diisi, unik, format nomor telepon seluler internasional (regex: `^\+?[1-9]\d{1,14}$`).
* `user_id`: Opsional, harus ada di tabel `users`.

### 3.5 Lifecycle
* **Created**: Dibuat bersamaan dengan registrasi siswa baru atau dibuat mandiri terlebih dahulu.
* **Bound**: Dikaitkan dengan model `User` ketika orang tua mengaktifkan akun login portalnya.
* **Archived**: Di-soft-delete jika seluruh siswa yang berada di bawah perwaliannya telah lulus atau dinonaktifkan dari madrasah.

### 3.6 Relationships
* `BelongsTo` ke `users` (Kredensial login portal).
* `HasMany` ke `students` (Anak-anak didik di bawah perwalian wali terkait).

### 3.7 Business Rules
* **BR-002**: Satu orang tua diperbolehkan memiliki relasi dengan lebih dari satu siswa aktif (misal: adik-kakak yang bersekolah di madrasah yang sama).
* **BR-011**: Nomor WhatsApp/telepon orang tua wajib berstatus unik untuk mencegah kekacauan pengiriman notifikasi tagihan finansial otomatis.

### 3.8 Audit Requirements
* Modifikasi pada kolom sensitif seperti `phone_number` wajib mencatat nilai lama (`old_values`) dan baru (`new_values`) demi keamanan forensik pelaporan keuangan.

---

## 4. Domain: Student (Siswa)

### 4.1 Purpose
Entitas pusat di madrasah. Berfungsi sebagai subjek didik dan subjek utama penargetan tagihan administrasi sekolah.

### 4.2 Ownership
* **Module Owner**: `app/Modules/Student` (Aggregate Root).

### 4.3 Fields (Database Schema)
* `id` (BIGINT, Primary Key, Auto-increment)
* `parent_id` (BIGINT, Foreign Key): Wali penanggung jawab keuangan.
* `class_id` (BIGINT, Foreign Key, Nullable): Kelas aktif berjalan.
* `nisn` (VARCHAR(10), Unique): Nomor Induk Siswa Nasional.
* `name` (VARCHAR(150))
* `gender` (ENUM('L', 'P'))
* `birth_place` (VARCHAR(100))
* `birth_date` (DATE)
* `is_active` (BOOLEAN): Status keaktifan siswa di sekolah. Default: `true`.
* `created_at` (TIMESTAMP)
* `updated_at` (TIMESTAMP)
* `deleted_at` (TIMESTAMP, Nullable)

### 4.4 Validation Rules
* `parent_id`: Wajib diisi, harus ada di tabel `parents`.
* `class_id`: Opsional (bisa NULL jika baru didaftarkan dan belum melalui pembagian kelas).
* `nisn`: Wajib diisi, unik, tepat 10 digit numerik murni.
* `name`: Wajib diisi, minimal 3 karakter, alfabet murni.
* `gender`: Wajib diisi, harus bernilai 'L' (Laki-laki) atau 'P' (Perempuan).
* `birth_date`: Wajib diisi, tanggal harus valid dan logis (tidak boleh di masa depan).

### 4.5 Lifecycle
* **Active**: Status awal saat didaftarkan. Siswa berhak menerima tagihan berjalan sekolah.
* **Graduated / Transferred**: Status keaktifan `is_active` diubah menjadi `false` ketika lulus atau mutasi keluar madrasah. Siswa tidak lagi ditarik tagihan keuangan periode berikutnya.
* **Soft-Deleted**: Disembunyikan dari visualisasi dasar jika terjadi kesalahan input pendaftaran (data fiktif).

### 4.6 Relationships
* `BelongsTo` ke `parents`.
* `BelongsTo` ke `classes`.

### 4.7 Business Rules
* **BR-001**: Satu siswa hanya boleh memiliki satu akun siswa aktif di madrasah.
* **BR-012**: Menonaktifkan siswa (`is_active = false`) otomatis menghentikan pembuatan program tagihan berkala baru untuk siswa tersebut, namun tidak menghapus kewajiban tagihan historis yang sudah berjalan sebelumnya.

### 4.8 Audit Requirements
* Segala perubahan status keaktifan (`is_active`) wajib mencatat alasan perubahan di log audit tingkat tinggi (**critical**).
