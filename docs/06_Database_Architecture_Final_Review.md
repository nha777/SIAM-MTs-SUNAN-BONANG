# Database Architecture Final Review - SIAM

Dokumen ini menyajikan audit arsitektur basis data final (*Database Architecture Final Review*) untuk skema Sprint 1 sebelum penulisan migrasi dilakukan. Evaluasi berfokus pada normalisasi data, kompatibilitas mesin basis data (MariaDB/MySQL), pencegahan referensi sirkular (*circular dependency*), fleksibilitas model perwalian, dan penanganan perilaku hapus (*ON DELETE behavior*).

---

## 1. Analisis Redundansi `academic_year_id` pada Tabel `classes`

### Pertanyaan Arsitektur:
*Apakah `academic_year_id` pada tabel `classes` diperlukan, atau cukup `semester_id` saja karena relasi tahun ajaran dapat diturunkan melalui semester?*

### Rekomendasi & Keputusan Audit:
**`academic_year_id` pada tabel `classes` sebaiknya DIHAPUS.**

#### Alasan Teknis & Normalisasi:
1. **Pencegahan Redundansi (Third Normal Form - 3NF)**:
   * Secara relasional, `classes` -> `semesters` -> `academic_years`.
   * Semester secara inheren terikat pada tepat satu Tahun Ajaran (`academic_year_id` adalah mandatory non-nullable foreign key di tabel `semesters`).
   * Menyimpan `academic_year_id` di tabel `classes` menciptakan ketergantungan transitif (*transitive dependency*) di mana $classes \rightarrow semester\_id \rightarrow academic\_year\_id$ dan sekaligus $classes \rightarrow academic\_year\_id$. Ini melanggar aturan normalisasi 3NF.
2. **Risiko Anomali Pembaruan (Update Anomaly)**:
   * Jika disimpan secara bersamaan, ada celah terjadinya inkonsistensi data di mana suatu baris kelas menunjuk ke `semester_id` milik Tahun Ajaran A (misal: "2026/2027 Ganjil"), tetapi kolom `academic_year_id` pada baris kelas yang sama menunjuk ke Tahun Ajaran B (misal: "2027/2028"). Kondisi korupsi integritas data ini sangat fatal bagi keakuratan laporan tagihan keuangan.
3. **Penyederhanaan Penargetan Keuangan**:
   * Walaupun argumen denormalisasi menyatakan kueri filter tanpa `JOIN` lebih cepat, pada praktiknya performa pencarian database relasional modern dengan indeks komposit jauh lebih unggul daripada menanggung risiko anomali data. Penargetan tagihan per semester atau per tahun ajaran dapat diselesaikan secara elegan dengan join sederhana yang aman.

---

## 2. Kompatibilitas Unique Constraint + Soft Delete pada MariaDB / MySQL

### Masalah Teknis:
Pada MariaDB/MySQL standar, nilai `NULL` dianggap sebagai nilai yang unik dan tidak sama dengan `NULL` lainnya. Akibatnya:
* Jika kita mendefinisikan indeks unik multi-kolom seperti `UNIQUE INDEX uq_students_nisn (nisn, deleted_at)`:
  * Ketika record siswa dihapus (nilai `deleted_at` diisi timestamp), kita bisa menghapusnya beberapa kali karena setiap timestamp berbeda.
  * **Namun**, saat siswa tersebut aktif (`deleted_at IS NULL`), MariaDB/MySQL akan mengizinkan pendaftaran berganda dengan NISN yang sama karena baris dengan nilai `NULL` pada `deleted_at` tidak saling membatalkan di bawah aturan keunikan default MySQL/MariaDB. Ini melanggar **Golden Rule #3 (Database is the Source of Truth)** karena siswa duplikat aktif dapat lolos masuk database.

### Solusi Kompatibilitas:
Untuk menjamin keunikan NISN dan Nomor Telepon Wali secara absolut pada record yang **aktif** (`deleted_at` adalah `NULL`), ada tiga opsi arsitektur:
1. **Opsi 1: Penggunaan Virtual Column (Rekomendasi Utama)**:
   * Tambahkan kolom virtual tergenerasi (*generated virtual column*) bernama `is_active_unique` yang bernilai `1` jika `deleted_at` kosong, dan `NULL` jika `deleted_at` terisi.
   * Terapkan index unik: `UNIQUE INDEX uq_nisn_active (nisn, is_active_unique)`. Karena MySQL/MariaDB mengizinkan duplikasi nilai `NULL` pada indeks unik, record yang di-soft-delete (`is_active_unique` = `NULL`) tidak akan membatasi keunikan record aktif baru. Record aktif (`is_active_unique` = `1`) tidak akan pernah bisa diduplikasi.
2. **Opsi 2: Menggunakan Nilai Sentinel Default (Alternatif Paling Portabel)**:
   * Alih-alih mengizinkan `deleted_at` bernilai `NULL`, gunakan nilai timestamp default yang tidak realistis (misal: `'1970-01-01 00:00:00'`) untuk record yang aktif.
   * Ketika record dihapus, ubah nilainya ke waktu saat ini.
   * Dengan cara ini, keunikan dijamin secara absolut melalui `UNIQUE (nisn, deleted_at)` tanpa melanggar aturan keunikan `NULL`.

---

## 3. Evaluasi Ketergantungan Sirkular (*Circular Dependency*)

### Analisis Alur Referensi:
Mari kita petakan alur kunci tamu (*foreign keys*) di skema:
1. `semesters.academic_year_id` $\rightarrow$ `academic_years.id`
2. `classes.semester_id` $\rightarrow$ `semesters.id`
3. `students.class_id` $\rightarrow$ `classes.id`
4. `students.parent_id` $\rightarrow$ `parents.id`
5. `parents.user_id` $\rightarrow$ `users.id`

### Hasil Evaluasi:
* **Bebas dari Circular Dependency**: Alur dependensi bersifat linier dan searah (acyclic graph). Tidak ada entitas yang saling menunjuk satu sama lain secara langsung atau berputar (misal: tidak ada kelas yang menunjuk langsung siswa sebagai ketua kelas di dalam tabel `classes` secara wajib yang berujung pada deadlock saat insert awal).
* **Insert Order Strategy**: Ketika menulis data seed atau transaksi pendaftaran baru, urutan eksekusi wajib dilakukan sebagai berikut:
  1. `users` (jika ada akun portal)
  2. `parents`
  3. `academic_years`
  4. `semesters`
  5. `classes`
  6. `students`

---

## 4. Evaluasi Model "Parent" untuk Kasus Wali Non-Orang-Tua

### Analisis Kebutuhan Operasional:
Siswa madrasah tidak jarang tinggal bersama wali yang bukan orang tua kandung (misal: paman, bibi, kakek, nenek, atau pengasuh pondok pesantren).
* Kolom saat ini: `father_name` dan `mother_name` bersifat `NOT NULL`.
* **Kelemahan**: Jika siswa tinggal dengan paman sebagai wali tunggal karena orang tuanya wafat atau berada di luar kota, pengisian formulir pendaftaran dipaksa memalsukan data ayah/ibu, atau sistem menolak pendaftaran karena kekosongan nilai `NOT NULL`.

### Solusi Desain (Peningkatan Integritas Bisnis):
Struktur tabel `parents` diubah namanya secara konseptual atau ditambahkan kolom tipe wali untuk mengakomodasi realitas operasional:
1. Ubah nama tabel dari `parents` menjadi `guardians` (Wali Murid) untuk representasi yang lebih inklusif dan akurat secara hukum administrasi sekolah.
2. Tambahkan kolom:
   * `guardian_relation` (ENUM('ayah', 'ibu', 'paman_bibi', 'kakek_nenek', 'lainnya'), NOT NULL, DEFAULT 'ayah')
   * `guardian_name` (VARCHAR(150), NOT NULL): Nama wali utama yang bertanggung jawab penuh atas administrasi dan notifikasi tagihan finansial.
   * Jadikan `father_name` dan `mother_name` sebagai kolom opsional (`Nullable`) yang berfungsi sebagai metadata tambahan profil siswa, bukan kontak penagihan utama.

---

## 5. Evaluasi Kebijakan Penghapusan (*ON DELETE Behavior*)

Berikut adalah tabel hasil audit kebijakan referensi integritas database untuk mengamankan data finansial:

| Hubungan Relasional (FK) | Kebijakan Saat Ini | Hasil Evaluasi & Rekomendasi | Alasan Keamanan Finansial |
| :--- | :--- | :--- | :--- |
| `semesters` $\rightarrow$ `academic_years` | `RESTRICT` | **Valid** | Menghindari terhapusnya periode semester berjalan jika tahun ajaran diutak-atik. |
| `classes` $\rightarrow$ `semesters` | `RESTRICT` | **Valid** | Mencegah kelas yatim (*orphan classes*) kehilangan periode akademisnya. |
| `students` $\rightarrow$ `classes` | `SET NULL` | **Valid** | Mengizinkan kelas dihapus (atau lulus) tanpa menghapus profil sejarah siswa tersebut. |
| `students` $\rightarrow$ `parents`/`guardians` | `RESTRICT` | **Valid (Sangat Penting)** | Menjamin profil penanggung jawab pembayaran tidak bisa dihapus selama rekam data siswa (yang memiliki tunggakan/sejarah invoice) masih eksis di sistem. |
| `parents`/`guardians` $\rightarrow$ `users` | `SET NULL` | **Valid** | Menjamin profil wali murid tetap tersimpan aman di database meskipun akun portal login mereka dihapus atau diblokir. |

---

## 6. Identifikasi Inkonsistensi Sebelum Penulisan Migrasi

1. **Inkoreksi Tipe Data Semester**:
   * Di beberapa perencanaan awal, semester dilambangkan dengan string biasa. Hal ini wajib di kunci menggunakan `ENUM('ganjil', 'genap')` langsung di level database untuk mencegah anomali masukan seperti "Ganjil", "ganjil_2", dll.
2. **Inkonsistensi Soft Deletes**:
   * Jika tabel induk (misal: `parents`) di-soft delete, query select default Laravel `whereNull('deleted_at')` akan mengabaikan record tersebut. Namun, anak relasinya (`students`) masih tetap terlihat "aktif" di tabelnya karena tidak ada cascade otomatis untuk soft-delete.
   * **Mitigasi**: Di level Service Domain, setiap tindakan soft-delete pada `parents/guardians` wajib memicu event untuk menonaktifkan status keaktifan seluruh siswa anaknya guna mencegah penagihan hantu (*phantom billing*).
