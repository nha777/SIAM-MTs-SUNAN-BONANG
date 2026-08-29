# Tinjauan Strategi Unique Constraint & Soft Deletes - SIAM

Tinjauan ini menganalisis empat strategi penanganan pembatasan keunikan (*Unique Constraint*) pada kolom yang menggunakan penghapusan logis (*Soft Deletes*) di lingkungan **MariaDB** dan **Laravel 12**. 

Analisis ini merujuk pada prinsip **ADR-004 (Evolutionary Architecture)** untuk menemukan solusi yang paling sederhana secara implementasi, minim gesekan (*friction*) dengan framework, namun tetap kokoh menjaga integritas data di level basis data (**Golden Rule #3**).

---

## 1. Analisis Perbandingan Strategi

### Strategi 1: Komposit `UNIQUE(nisn, deleted_at)`
* **Mekanisme**: Membuat indeks unik gabungan antara kolom data (`nisn`) dan kolom pencatat waktu hapus (`deleted_at`).
* **Kompabilitas MariaDB/MySQL**: 
  * Di MariaDB/MySQL, nilai `NULL` tidak dianggap setara dengan `NULL` lainnya (`NULL != NULL`).
  * Akibatnya, indeks unik tidak akan mendeteksi duplikasi pada baris-baris aktif di mana `deleted_at IS NULL`. Sistem dapat memiliki banyak siswa aktif dengan NISN yang sama.
  * Sebaliknya, untuk data yang dihapus (memiliki nilai timestamp nyata), keunikan justru dipaksakan secara ketat. Ini salah sasaran karena kita justru ingin memperbolehkan duplikasi pada data historis yang sudah dihapus.
* **Kelebihan**: Sangat mudah ditulis di kelas migrasi Laravel (`$table->unique(['nisn', 'deleted_at'])`).
* **Kekurangan**: **Struktural rusak** di MariaDB/MySQL. Gagal melindungi integritas data aktif.
* **Kesesuaian ADR-004**: Tidak layak digunakan karena tidak aman secara arsitektur.

---

### Strategi 2: Generated Virtual Column (`active_nisn`)
* **Mekanisme**: 
  * Membuat kolom virtual tergenerasi (*Generated Virtual Column*) di database yang secara otomatis menghitung nilai berdasarkan kondisi `deleted_at`.
  * Formula: `active_nisn AS (IF(deleted_at IS NULL, nisn, NULL))`.
  * Membuat indeks unik tunggal pada kolom virtual ini: `UNIQUE INDEX uq_active_nisn (active_nisn)`.
  * Karena MariaDB memperbolehkan banyak nilai `NULL` pada indeks unik, record yang di-soft-delete (`active_nisn = NULL`) tidak akan saling bentrok. Namun, record aktif (`active_nisn = nisn`) dipaksa unik secara absolut di level database.
* **Kompabilitas MariaDB/MySQL**: Didukung penuh secara luas sejak MariaDB 10.0.22+ dan MySQL 5.7+.
* **Dukungan Laravel 12**: Didukung secara native di dalam Blueprint skema Laravel:
  ```php
  $table->string('active_nisn')
        ->virtualAs("IF(deleted_at IS NULL, nisn, NULL)")
        ->nullable();
  $table->unique('active_nisn');
  ```
* **Kelebihan**: 
  * **100% Aman**: Integritas data dijamin secara mutlak di level database.
  * **Zero Friction**: Tidak membutuhkan kustomisasi pada trait `SoftDeletes` bawaan Eloquent Laravel. Laravel tetap bekerja dengan kolom `deleted_at` standar tanpa kendala.
  * Mengizinkan duplikasi tak terbatas pada data historis yang sudah dihapus.
* **Kekurangan**: Membutuhkan sedikit sintaks tambahan di file migrasi.
* **Kesesuaian ADR-004**: **Sangat Tinggi**. Solusi cerdas yang memanfaatkan kapabilitas native DB untuk menyederhanakan kode aplikasi.

---

### Strategi 3: Soft Delete Archive Table
* **Mekanisme**: 
  * Memisahkan penyimpanan fisik data aktif dan data historis.
  * Tabel `students` hanya berisi data aktif dengan indeks unik murni `UNIQUE(nisn)`.
  * Ketika record dihapus, record tersebut dipindahkan (*move*) ke tabel `students_archive`, kemudian dihapus secara permanen dari tabel utama.
* **Kelebihan**: Mengatasi masalah keunikan secara tuntas tanpa trik indeks khusus. Tabel utama tetap ramping.
* **Kekurangan**: 
  * **Kompleksitas Tinggi**: Merusak trait `SoftDeletes` standar Laravel. Kita harus mengesampingkan lifecycle Eloquent secara manual.
  * Merusak relasi bawaan Eloquent (`belongsTo`, `hasMany`) karena data historis berpindah tabel fisik.
  * Memerlukan pemeliharaan dua skema tabel yang identik secara paralel.
* **Kesesuaian ADR-004**: **Sangat Rendah (Over-engineering)**. Melanggar prinsip evolusioner dengan memperkenalkan kompleksitas operasional yang terlalu dini di Sprint 1.

---

### Strategi 4: Application-level Validation + DB Non-Unique Index
* **Mekanisme**: 
  * Database hanya dipasangi indeks biasa (*Non-Unique Index*) pada kolom `nisn` untuk kecepatan kueri.
  * Keunikan sepenuhnya dipelihara oleh kode aplikasi melalui aturan validasi request di Laravel:
    ```php
    'nisn' => 'required|unique:students,nisn,NULL,id,deleted_at,NULL'
    ```
* **Kelebihan**: Sangat mudah diimplementasikan tanpa modifikasi basis data.
* **Kekurangan**: 
  * **Rentang Balapan (*Race Conditions*)**: Jika dua request pendaftaran dengan NISN yang sama masuk secara simultan (milidetik yang sama), keduanya akan lolos validasi Laravel dan database akan menyimpan data duplikat aktif.
  * **Bypass Keamanan**: Tidak ada pelindung jika data dimasukkan melalui database seeder, raw SQL query, console command, atau integrasi pihak ketiga yang mem-bypass Form Request.
  * Melanggar **Golden Rule #3 (Database is the Source of Truth)**.
* **Kesesuaian ADR-004**: Rendah. Terlalu rapuh untuk modul dengan akurasi finansial tinggi.

---

## 2. Matriks Trade-Off Keputusan

| Dimensi Evaluasi | Strategi 1: Komposit | Strategi 2: Virtual Column | Strategi 3: Archive Table | Strategi 4: App-Level Only |
| :--- | :---: | :---: | :---: | :---: |
| **Integritas DB** | ❌ Gagal |  Sempurna |  Sempurna | ❌ Rentan |
| **Sederhana (Koding)** |  Tinggi |  Tinggi | ❌ Sangat Rumit |  Sangat Tinggi |
| **Friction dengan Laravel**|  Tidak ada |  Tidak ada | ❌ Sangat Tinggi |  Tidak ada |
| **Dukungan MariaDB** |  Penuh |  Penuh (10.0+) |  Penuh |  Penuh |
| **Kemudahan Pemeliharaan**|  Tinggi |  Tinggi | ❌ Rendah |  Tinggi |

---

## 3. Keputusan Akhir Rekomendasi (ADR-004 Compliance)

Berdasarkan analisis trade-off di atas, **Strategi 2 (Generated Virtual Column)** ditetapkan sebagai solusi standar untuk seluruh modul di SIAM (Sprint 1 dan seterusnya).

### Alasan Utama Pemilihan:
1. **Keamanan Maksimal**: Mencegah terjadinya data duplikat aktif secara mutlak di level engine MariaDB tanpa kompromi (*Zero Race Conditions*).
2. **Kepatuhan Terhadap Framework**: Tidak ada perubahan kode pada model Eloquent atau trait `SoftDeletes` bawaan Laravel 12. Pengembang tetap menulis query seperti biasa (`Student::create()`, `Student::destroy()`).
3. **Evolusioner (ADR-004)**: Solusi ini tidak menambah kompleksitas arsitektur seperti tabel arsip baru (Strategi 3), tetapi memberikan keamanan jauh lebih tinggi daripada sekadar validasi aplikasi (Strategi 4). Solusi ini memanfaatkan fitur bawaan database relasional modern untuk menyelesaikan masalah pelik secara anggun.
