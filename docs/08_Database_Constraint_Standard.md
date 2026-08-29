# Standar Tata Kelola Batasan Basis Data & Soft Deletes - SIAM

Dokumen ini mendefinisikan Standar Resmi Tata Kelola Basis Data (*Database Governance Standard*) di lingkungan **SIAM (Sistem Informasi Akademik Madrasah)**. Standar ini memecahkan konflik klasik antara fitur **Soft Deletes** (penghapusan logis) dan **Unique Constraints** (pembatasan keunikan) pada mesin basis data **MariaDB/MySQL** dengan integrasi **Laravel 12**.

---

## 1. Deklarasi Standar Tunggal Proyek (Single Project Standard)

Untuk menjamin kepatuhan penuh terhadap **Golden Rule #3: Database is the Source of Truth**, SIAM menetapkan kebijakan tunggal yang wajib dipatuhi di seluruh migrasi tabel:

> **"Setiap tabel yang menggunakan fitur Soft Deletes (kolom `deleted_at`) dan membutuhkan pembatasan keunikan (Unique Constraint) pada satu atau beberapa kolom, WAJIB menggunakan strategi Generated Virtual Column."**

### Konvensi Penamaan (*Naming Convention*):
* Untuk kolom unik dasar `nama_kolom` (misal: `nisn`), wajib dibuat kolom virtual pendamping dengan nama: **`active_nama_kolom`** (misal: `active_nisn`).
* Rumus standar kolom virtual di MariaDB:
  ```sql
  active_nama_kolom AS (IF(deleted_at IS NULL, nama_kolom, NULL)) VIRTUAL
  ```
* Indeks unik diterapkan **hanya** pada kolom virtual tersebut, bukan pada kolom dasar maupun komposit `['nama_kolom', 'deleted_at']`.

---

## 2. Evaluasi Penerapan Kolom Virtual pada Entitas Core

Berikut adalah evaluasi kelayakan penerapan strategi kolom virtual tergenerasi pada empat entitas utama SIAM:

### 2.1 Entitas: `students.nisn`
* **Status Evaluasi**: **WAJIB MENGGUNAKAN** (`active_nisn`).
* **Justifikasi**: 
  * NISN adalah pengenal unik nasional siswa yang tidak boleh ganda di antara siswa aktif.
  * Jika rekam data siswa lama di-soft-delete (karena pembatalan pendaftaran atau kesalahan entri), sistem harus dapat menerima pendaftaran siswa baru menggunakan NISN tersebut tanpa bentrok dengan data sampah di masa lalu.

### 2.2 Entitas: `guardians.phone_number`
* **Status Evaluasi**: **WAJIB MENGGUNAKAN** (`active_phone_number`).
* **Justifikasi**:
  * Nomor telepon seluler berfungsi sebagai pengenal unik untuk integrasi WhatsApp Gateway dan sistem autentikasi OTP.
  * Jika akun wali murid dinonaktifkan (di-soft-delete), nomor telepon tersebut harus dapat digunakan kembali untuk pendaftaran akun baru tanpa hambatan.

### 2.3 Entitas: `academic_years.name`
* **Status Evaluasi**: **WAJIB MENGGUNAKAN** (`active_academic_year_name`).
* **Justifikasi**:
  * Format nama tahun ajaran (e.g., "2026/2027") harus unik agar tidak ada kerancuan periode.
  * Jika sebuah draf tahun ajaran di-soft-delete sebelum dipublikasikan karena salah ketik, administrator harus bisa membuat ulang tahun ajaran dengan nama yang sama persis secara bersih.

### 2.4 Entitas: `users.email`
* **Status Evaluasi**: **KONDISIONAL (Bergantung pada Soft Delete Policy di level User)**.
* **Justifikasi & Analisis**:
  * Jika tabel `users` **menggunakan** `SoftDeletes`: Kolom virtual `active_email` **wajib** digunakan. Ini menghindari skenario di mana pengguna lama yang telah dihapus menghalangi pengguna baru dengan alamat email yang sama untuk mendaftar.
  * Jika tabel `users` **TIDAK menggunakan** `SoftDeletes` (dihapus secara permanen/hard-delete): Kolom virtual **tidak diperlukan**. Indeks unik biasa `UNIQUE(email)` sudah cukup dan lebih efisien.
  * **Keputusan Tata Kelola**: Demi konsistensi keamanan riwayat transaksi (terutama karena `user_id` dirujuk oleh rekam audit log keuangan), SIAM mengadopsi kebijakan **Soft Delete pada seluruh tabel User & Profil**. Dengan demikian, tabel `users` menggunakan kolom virtual **`active_email`** sebagai standar proyek.

---

## 3. Matriks Standar Atribut Kolom Unik SIAM

| Nama Tabel | Kolom Unik Dasar | Kolom Generated Virtual | Indeks Unik Basis Data | Deskripsi Fungsi Bisnis |
| :--- | :--- | :--- | :--- | :--- |
| `users` | `email` | `active_email` | `UNIQUE(active_email)` | Kredensial masuk portal sistem. |
| `students` | `nisn` | `active_nisn` | `UNIQUE(active_nisn)` | Nomor Induk Siswa Nasional tunggal. |
| `guardians` | `phone_number` | `active_phone_number` | `UNIQUE(active_phone_number)` | Kontak utama WhatsApp Gateway. |
| `academic_years`| `name` | `active_name` | `UNIQUE(active_name)` | Nama periode tahun ajaran. |

---

## 4. Keuntungan Tata Kelola (Governance Benefits)

1. **Akurasi Forensik Finansial (Sovereignty Retention)**:
   * Melalui pemanfaatan soft deletes yang aman dari bentrok keunikan, data historis tagihan dan pembayaran yang menunjuk ke ID siswa lama yang telah dihapus tidak akan menjadi yatim (*orphan records*) ataupun hilang dari histori audit audit eksternal.
2. **Standardisasi Tim Pengembang (Developer Onboarding)**:
   * Dengan menetapkan aturan tunggal yang baku, seluruh pengembang modul di masa depan (misal: Modul Pembayaran, Guru, Kelas) memiliki panduan seragam tanpa perlu berdiskusi ulang mengenai penanganan anomali `NULL` di MariaDB.
3. **Efisiensi Kueri (Query Optimization)**:
   * Kolom virtual tergenerasi di MariaDB dikelola secara internal oleh mesin basis data. Indeks unik pada kolom virtual ini memiliki kecepatan pencarian ($O(\log N)$) yang sama persis dengan indeks unik pada kolom fisik tradisional, tanpa membebani performa aplikasi Laravel.
