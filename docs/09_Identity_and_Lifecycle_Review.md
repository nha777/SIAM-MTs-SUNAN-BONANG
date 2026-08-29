# Tinjauan Identitas & Siklus Hidup Data (Identity & Lifecycle Review) - SIAM

Dokumen ini menganalisis tata kelola siklus hidup data (*Data Lifecycle Governance*) untuk entitas yang memiliki batasan keunikan (*Unique Constraint*) di SIAM. Tinjauan ini membagi klasifikasi entitas menjadi **Reusable** (dapat digunakan kembali setelah dihapus) atau **Immutable** (abadi dan tidak boleh diduplikasi selamanya) berdasarkan aspek forensik audit, konsistensi transaksi, dan keamanan sistem.

---

## 1. Matriks Klasifikasi Tata Kelola Identitas

| Kolom Basis Data | Klasifikasi Tata Kelola | Kebijakan Reusability | Penanganan Database (`deleted_at`) |
| :--- | :---: | :--- | :--- |
| **`users.email`** | **Reusable** | Boleh digunakan kembali oleh akun baru jika akun lama telah dihapus (*soft-deleted*). | `active_email` (Virtual Column) membebaskan email lama agar bisa didaftarkan ulang. |
| **`students.nisn`** | **Immutable** | Secara bisnis bersifat abadi (tunggal per manusia). Hanya boleh di-reuse jika penghapusan disebabkan oleh kesalahan input administratif (*human error*). | `active_nisn` (Virtual Column) membebaskan NISN hanya untuk koreksi data fiktif/salah input. |
| **`guardians.phone_number`** | **Reusable** | Wajib dapat digunakan kembali karena nomor seluler sering didaur ulang (*recycled*) oleh operator telekomunikasi. | `active_phone_number` (Virtual Column) membebaskan nomor agar bisa digunakan wali murid lain di masa depan. |
| **`academic_years.name`** | **Immutable** | Bersifat periodik historis. Tidak boleh ada duplikasi nama tahun ajaran yang pernah aktif berjalan sepanjang sejarah sekolah. | `active_name` (Virtual Column) membebaskan nama tahun ajaran hanya jika draf awal dihapus sebelum masa aktif. |

---

## 2. Analisis Mendalam Per Entitas

### 2.1 `users.email` (Kredensial Login)

* **Apakah boleh digunakan ulang setelah soft-delete?**
  * **Ya.** Jika seorang staf, guru, atau wali murid keluar dari sistem madrasah dan akunnya dinonaktifkan secara logis (*soft-delete*), alamat email tersebut harus dapat digunakan kembali di kemudian hari jika ia mendaftar ulang, atau jika email tersebut kini dimiliki oleh staf baru.
* **Apakah identitas harus abadi selamanya?**
  * **Tidak.** Email adalah atribut komunikasi yang dinamis dan dapat berubah kepemilikannya.
* **Dampak terhadap Audit Log**:
  * Log audit lama mencatat transaksi berdasarkan surrogate key `user_id` (integer) dan merekam snapshot nama/email pada waktu kejadian. Penggunaan ulang email pada `user_id` baru tidak akan mencampuradukkan log historis karena memiliki ID primer yang berbeda secara fisik.
* **Dampak terhadap Histori Transaksi**:
  * Histori transaksi finansial atau akademik merujuk pada `user_id` murni. Tidak ada dampak anomali data pada laporan keuangan historis.
* **Dampak terhadap Keamanan**:
  * **Sangat Tinggi**. Saat akun lama di-soft-delete, seluruh token autentikasi aktif (Bearer Token, Session, Password Reset Token) yang terkait dengan email tersebut wajib dihancurkan (*invalidated*). Pengguna baru yang mendaftar dengan email yang sama tidak boleh mewarisi riwayat akses, password lama, atau hak akses (roles/permissions) dari akun lama.

---

### 2.2 `students.nisn` (Nomor Induk Siswa Nasional)

* **Apakah boleh digunakan ulang setelah soft-delete?**
  * **Secara Hukum Bisnis: Tidak.** NISN diterbitkan oleh Kemendikbudristek RI dan melekat secara unik pada satu individu selamanya.
  * **Secara Pengecualian Sistem: Ya.** Penggunaan ulang hanya diperbolehkan jika record sebelumnya dihapus karena *human error* (misal: salah input digit NISN pada Siswa A, lalu rekam data tersebut dihapus, sehingga NISN yang benar harus dibebaskan agar bisa diinput ulang ke profil Siswa A yang baru atau Siswa B yang sebenarnya).
* **Apakah identitas harus abadi selamanya?**
  * **Ya.** NISN yang valid harus abadi merujuk pada satu siswa yang sama untuk menghindari pemalsuan akademik.
* **Dampak terhadap Audit Log**:
  * Setiap tindakan modifikasi atau penghapusan NISN wajib mencatat payload lengkap (`old_nisn` dan `new_nisn`) dengan status keamanan **Critical** guna mencegah manipulasi data siswa fiktif.
* **Dampak terhadap Histori Transaksi**:
  * Transaksi tagihan sekolah dikunci menggunakan `student_id` (Surrogate Key). Perubahan NISN karena koreksi typo tidak akan memutuskan sejarah pembayaran SPP siswa terkait.
* **Dampak terhadap Keamanan**:
  * Mencegah pembajakan identitas akademik siswa (*identity theft*) di mana siswa yang dikeluarkan tidak boleh digantikan identitasnya oleh siswa baru menggunakan NISN yang sama.

---

### 2.3 `guardians.phone_number` (WhatsApp Gateway / Kontak Wali)

* **Apakah boleh digunakan ulang setelah soft-delete?**
  * **Ya.** Di Indonesia, nomor kartu perdana seluler sangat sering hangus dan diproduksi ulang oleh operator seluler (*recycled SIM card*). Wali murid baru di tahun-tahun mendatang sangat mungkin mendapatkan nomor telepon bekas wali murid lama yang sudah lulus.
* **Apakah identitas harus abadi selamanya?**
  * **Tidak.** Nomor kontak murni bersifat transient dan sangat bergantung pada kepemilikan pihak ketiga (operator seluler).
* **Dampak terhadap Audit Log**:
  * Riwayat pengiriman pesan WhatsApp tagihan keuangan tetap terikat pada `guardian_id` historis. Pergantian nomor tidak merusak log pesan yang sudah terkirim ke pemilik lama.
* **Dampak terhadap Histori Transaksi**:
  * Tagihan keuangan tetap menunjuk ke `guardian_id` yang sah, bukan string nomor telepon.
* **Dampak terhadap Keamanan**:
  * **Kritis**. Ketika nomor telepon diubah oleh pengguna atau dinonaktifkan:
    1. Sistem wajib menghapus nomor tersebut dari database sesi aktif OTP.
    2. Segala verifikasi 2FA berbasis WhatsApp wajib di-reset pada akun lama untuk mencegah pemilik nomor baru (hasil daur ulang SIM) masuk ke portal wali murid secara ilegal melalui metode "Forgot Password" berbasis nomor telepon.

---

### 2.4 `academic_years.name` (Tahun Ajaran)

* **Apakah boleh digunakan ulang setelah soft-delete?**
  * **Tidak.** Periode waktu akademik bersifat linear (selalu maju ke depan). Tahun ajaran "2026/2027" hanya terjadi sekali dalam sejarah sekolah.
  * **Kecuali**: Jika admin melakukan salah input saat pembuatan draf (misal typo menulis "2026/202" lalu di-soft delete), maka nama pembetulannya harus bisa ditulis ulang.
* **Apakah identitas harus abadi selamanya?**
  * **Ya.** Nama periode tidak boleh berubah atau diduplikasi setelah transaksi keuangan pertama diterbitkan untuk menjaga validitas laporan akuntansi tahunan.
* **Dampak terhadap Audit Log & Transaksi**:
  * Sangat besar. Jika nama tahun ajaran yang memiliki riwayat keuangan diubah, seluruh laporan audit neraca keuangan tahunan sekolah akan mengalami anomali kalkulasi.
* **Dampak terhadap Keamanan**:
  * Menjamin tidak ada manipulasi data masa lalu oleh oknum internal (*insider threat*) untuk mencuci riwayat tagihan yang belum lunas di tahun-tahun ajaran lampau.

---

## 3. Kebijakan Implementasi Forensik (Forensic Best Practices)

Demi menjaga kesucian data historis di bawah prinsip arsitektur modular monolith, SIAM menerapkan 3 aturan forensik:

1. **Surrogate Key Dominance**:
   * Seluruh relasi *foreign key* tabel transaksi wajib merujuk pada `ID` auto-increment murni (Surrogate Key), **BUKAN** pada kolom unik bisnis seperti `nisn`, `email`, atau `phone_number`. Ini menjamin struktur relasi tidak akan pernah pecah meskipun data bisnis mengalami koreksi typo atau daur ulang.
2. **Immutable Audit Trails**:
   * Log audit yang disimpan melalui trait `HasAuditLogs` wajib menyertakan nilai literal lengkap data pada saat kejadian (*snapshotting*), bukan sekadar relasi dinamis. Jika data siswa dihapus, log audit harus menyimpan string nama siswa tersebut agar riwayat tetap terbaca manusia tanpa perlu melakukan join ke tabel yang datanya sudah hilang.
3. **Hard-Delete Prohibition on Production**:
   * Operasi `forceDelete()` (penghapusan permanen baris database) **dilarang keras** dijalankan pada tabel master akademik dan profil pengguna di lingkungan produksi untuk mencegah hilangnya jejak bukti audit keuangan.
