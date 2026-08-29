# Arsitektur & Analisis Kesenjangan (Architecture & Gap Analysis)
## Sprint 1G – Audit Trail & Forensic Logging Design

## 1. Pendahuluan & Latar Belakang

Sistem Informasi Akademik Madrasah (SIAM) merupakan aplikasi berbasis arsitektur **Modular Monolith (ADR-001)** dengan mengimplementasikan **Repository Pattern (ADR-002)** dan menganut prinsip **Database as the Source of Truth (Golden Rule #3)**. Pada tahap pengembangan Sprint 1G ini, SIAM memerlukan penguatan pada aspek transparansi, akuntabilitas, keamanan, dan forensik digital melalui sistem **Audit Trail & Forensic Logging**.

Desain arsitektur ini dirancang mengacu pada keputusan arsitektur yang telah dibekukan sebelumnya, khususnya **ADR-003 (Event Driven Audit Logging)** dan **ADR-004 (Evolutionary Architecture)**. Melalui pendekatan Event-Driven, pencatatan aktivitas bisnis krusial akan didekopel (decoupled) dari siklus request-response HTTP utama untuk menjaga performa dan skalabilitas sistem modular monolith.

Dokumen ini menganalisis kesenjangan (gap analysis) dari arsitektur audit logging yang saat ini sudah berjalan secara sederhana melalui Trait `HasAuditLogs`, merumuskan spesifikasi lengkap, serta menyusun cetak biru (blueprint) implementasi sebelum kode program ditulis.

---

## 2. Audit Event Catalog

Seluruh aktivitas mutasi data dan autentikasi wajib direkam secara granular. Berikut adalah katalog event audit transaksional yang harus didukung oleh SIAM:

### A. Domain Student & Guardian

| Kode Event | Nama Event | Deskripsi | Aktor | Target Auditable | Detail Konteks Tambahan |
|---|---|---|---|---|---|
| `student_created` | Siswa Baru Didaftarkan | Ditrigger saat data siswa baru berhasil dimasukkan ke sistem. | Tata Usaha / Admin | `Student` | Menyimpan NISN awal, Nama, dan Kelas terkait. |
| `student_updated` | Data Siswa Diubah | Ditrigger saat terjadi perubahan profil atau atribut siswa. | Tata Usaha / Admin | `Student` | Menyimpan payload komparasi delta (`old_values` vs `new_values`). |
| `student_deleted` | Siswa Dihapus (Soft Delete) | Ditrigger saat siswa dinonaktifkan/dihapus secara logis. | Tata Usaha / Admin | `Student` | Menyimpan alasan/status penghapusan. |
| `student_restored` | Siswa Dipulihkan | Ditrigger saat data siswa yang telah terhapus dipulihkan kembali. | Tata Usaha / Admin | `Student` | Mengembalikan status operasional siswa menjadi aktif. |
| `student_graduated` | Siswa Diluluskan | Status siswa diubah menjadi 'lulus'. | Tata Usaha / Admin | `Student` | Menyimpan Tahun Akademik kelulusan. |
| `student_transferred` | Siswa Mutasi | Status siswa diubah menjadi 'mutasi'. | Tata Usaha / Admin | `Student` | Menyimpan catatan sekolah tujuan atau asal mutasi. |
| `student_suspended` | Siswa Diskors | Status siswa diubah menjadi 'skorsing'. | Tata Usaha / Admin | `Student` | Menyimpan catatan masa berlaku skorsing. |
| `guardian_created` | Wali Siswa Dibuat | Ditrigger saat wali siswa baru didaftarkan. | Tata Usaha / Admin | `Guardian` | Menyimpan nama wali dan nomor kontak. |
| `guardian_updated` | Wali Siswa Diubah | Profil wali siswa diperbarui. | Tata Usaha / Admin | `Guardian` | Menyimpan delta perubahan informasi kontak/alamat. |
| `guardian_deleted` | Wali Siswa Dihapus | Wali siswa dihapus (Soft Delete/Hard Delete). | Tata Usaha / Admin | `Guardian` | Menyimpan referensi siswa yang terdampak kehilangan wali. |
| `guardian_restored` | Wali Siswa Dipulihkan | Memulihkan data wali siswa yang terhapus. | Tata Usaha / Admin | `Guardian` | Mengembalikan relasi wali ke siswa terkait. |

### B. Domain Academic

| Kode Event | Nama Event | Deskripsi | Aktor | Target Auditable | Detail Konteks Tambahan |
|---|---|---|---|---|---|
| `academic_year_created` | Tahun Ajaran Baru Dibuat | Tahun akademik baru ditambahkan ke database. | Super Admin / Admin | `AcademicYear` | Format nama tahun (contoh: "2026/2027"). |
| `academic_year_updated` | Tahun Ajaran Diubah | Modifikasi nama atau metadata tahun ajaran. | Super Admin / Admin | `AcademicYear` | Delta perubahan nama tahun ajaran. |
| `academic_year_activated` | Tahun Ajaran Diaktifkan | Tahun ajaran tertentu ditetapkan sebagai periode aktif sistem. | Super Admin / Admin | `AcademicYear` | Menonaktifkan tahun akademik aktif sebelumnya secara otomatis. |
| `academic_year_deleted` | Tahun Ajaran Dihapus | Penghapusan catatan tahun ajaran. | Super Admin / Admin | `AcademicYear` | Hanya diperbolehkan jika belum ada transaksi di dalamnya. |
| `semester_created` | Semester Baru Dibuat | Penambahan semester (ganjil/genap) untuk suatu tahun ajaran. | Super Admin / Admin | `Semester` | Menghubungkan semester ke `academic_year_id`. |
| `semester_updated` | Semester Diubah | Perubahan nama semester atau periode operasional. | Super Admin / Admin | `Semester` | Delta perubahan atribut semester. |
| `semester_activated` | Semester Diaktifkan | Semester tertentu diaktifkan untuk memulai tahun ajaran baru. | Super Admin / Admin | `Semester` | Mengunci semester lama dan mengaktifkan semester baru. |
| `semester_deleted` | Semester Dihapus | Penghapusan semester dari konfigurasi. | Super Admin / Admin | `Semester` | Hanya diperbolehkan jika tidak memiliki kelas aktif. |
| `class_created` | Kelas Baru Dibuat | Kelas akademik baru ditambahkan ke semester aktif. | Tata Usaha / Admin | `AcademicClass` | Menyimpan Nama Kelas, Grade (7, 8, 9), dan Semester terkait. |
| `class_updated` | Kelas Diubah | Perubahan nama kelas, tingkat grade, atau relasi semester. | Tata Usaha / Admin | `AcademicClass` | Delta perubahan atribut kelas. |
| `class_deleted` | Kelas Dihapus | Penghapusan entitas kelas akademik. | Tata Usaha / Admin | `AcademicClass` | Memastikan relasi ke siswa dikosongkan/dialihkan. |

### C. Domain Authentication

| Kode Event | Nama Event | Deskripsi | Aktor | Target Auditable | Detail Konteks Tambahan |
|---|---|---|---|---|---|
| `login_success` | Login Berhasil | Pengguna berhasil masuk ke sistem dengan kredensial valid. | Pengguna | `User` | Menyimpan snapshot IP, User Agent, dan Role saat login. |
| `login_failed` | Login Gagal | Upaya login gagal karena password salah atau user tidak ditemukan. | Sistem | `User` / Guest | Menyimpan username/email yang dicoba beserta detail IP Address. |
| `logout` | Logout Berhasil | Sesi pengguna diakhiri secara sukarela atau akibat timeout. | Pengguna | `User` | Mengosongkan token aktif. |

---

## 3. Audit Severity Matrix

Untuk mempermudah monitoring keamanan dan pelaporan forensik, seluruh event audit diklasifikasikan ke dalam tiga tingkat keparahan (severity level) dengan panduan dan skenario penanganan sebagai berikut:

```
+------------------------------------------------------------+
|                       SEVERITY LEVELS                      |
+------------------------------------------------------------+
|  [INFO]     -> Aktivitas operasional bisnis standar         |
|  [WARNING]  -> Mutasi struktural, penonaktifan data & auth |
|  [CRITICAL] -> Perubahan konfigurasi sistem global & anomali|
+------------------------------------------------------------+
```

### 1. **INFO**
*   **Alasan Klasifikasi**: Merupakan aktivitas operasional harian yang bersifat reguler (Business-as-Usual). Tidak menimbulkan risiko keamanan langsung atau perubahan mendasar pada konfigurasi sistem global, namun wajib dicatat demi transparansi audit internal.
*   **Contoh Event**:
    - `student_created`
    - `student_updated`
    - `guardian_created`
    - `guardian_updated`
    - `class_created`
    - `class_updated`
    - `academic_year_created`
    - `semester_created`
    - `login_success`
    - `logout`

### 2. **WARNING**
*   **Alasan Klasifikasi**: Aktivitas yang melibatkan penghapusan data secara logis (soft-delete), pemulihan data (restore), atau aktivitas autentikasi yang gagal yang berpotensi menjadi indikasi awal pelanggaran akses. Event dalam kategori ini membutuhkan perhatian jika terjadi lonjakan frekuensi dalam rentang waktu yang singkat.
*   **Contoh Event**:
    - `student_deleted`
    - `student_restored`
    - `guardian_deleted`
    - `guardian_restored`
    - `class_deleted`
    - `academic_year_deleted`
    - `semester_deleted`
    - `login_failed` (Wajib dimonitor untuk pencegahan Brute Force)

### 3. **CRITICAL**
*   **Alasan Klasifikasi**: Aktivitas administratif berisiko tinggi yang berdampak langsung pada kelangsungan bisnis keseluruhan sistem (system-wide lifecycle changes) atau status hukum siswa (kelulusan/skorsing/keluar). Perubahan pada tingkat ini dapat mempengaruhi integritas data historis dan konfigurasi transaksional aktif secara global.
*   **Contoh Event**:
    - `academic_year_activated` (Mempengaruhi tahun ajaran aktif sistem)
    - `semester_activated` (Mengubah masa transaksional aktif)
    - `student_graduated` (Merubah status hukum kelulusan siswa)
    - `student_suspended` (Membatasi hak akses akademik siswa)
    - `student_transferred` (Mengeluarkan siswa dari ekosistem madrasah)

---

## 4. Evaluasi & Desain Basis Data (Audit Log Database Design)

### Analisis Kesenjangan (Gap Analysis) Skema Saat Ini
Tabel `audit_logs` saat ini telah diimplementasikan dalam berkas migrasi `2026_07_16_000001_create_audit_logs_table.php` dengan struktur berikut:
*   `id`, `event_id` (UUID), `request_id` (UUID index)
*   `severity` (enum: 'info', 'warning', 'critical')
*   `user_id` (Foreign Key ke `users` tabel, nullable, onDelete: set null)
*   `event` (string)
*   `auditable_type` (string), `auditable_id` (integer)
*   `old_values` (JSON), `new_values` (JSON)
*   `ip_address`, `user_agent`
*   `created_at`

### Penilaian Kecukupan (Adequacy Assessment)
Skema database saat ini **belum memadai untuk kebutuhan forensik tingkat lanjut (Advanced Forensic Requirements)** karena alasan berikut:
1.  **Actor Loss on Delete**: Kolom `user_id` diset ke `null` jika user dihapus (`onDelete('set null')`). Hal ini menyebabkan kita kehilangan informasi krusial mengenai "siapa" yang melakukan aksi tersebut setelah akunnya dihapus.
2.  **No Actor Type Polymorphism**: Pencatatan hanya mengasumsikan aktor adalah entitas `User`. Dalam pengembangan masa depan arsitektur evolusioner, aktor bisa saja berupa entitas sistem otomatis (Cron Job, API Token External, dsb) sehingga pendekatan Polymorphic Actor diperlukan.
3.  **No Metadata Field**: Tidak ada kolom `metadata` fleksibel untuk merekam snapshot peran pengguna (user roles), email pengguna pada saat transaksi terjadi, atau konteks tambahan lainnya yang tidak masuk ke dalam `old_values` atau `new_values`.

### Rekomendasi Pembaruan Skema Database (Struktur Final - Kompatibel MariaDB & MySQL)

Untuk mendukung kebutuhan audit forensik yang tangguh tanpa melanggar prinsip normalisasi berlebih serta mematuhi **Database Freeze v1** (MariaDB/MySQL), kita merancang struktur tabel `audit_logs` yang optimal untuk MariaDB 10.x+ dan MySQL 8+:

```sql
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id CHAR(36) NOT NULL UNIQUE, -- Representasi string UUID standar
    request_id CHAR(36) NOT NULL,
    severity VARCHAR(20) NOT NULL DEFAULT 'info',
    
    -- Actor Polymorphic Columns (Nullable untuk System/Guest events)
    actor_id BIGINT UNSIGNED NULL,
    actor_type VARCHAR(255) NULL,
    
    event_name VARCHAR(100) NOT NULL, -- Diisi dari ENUM internal (misal: AuditEvent::STUDENT_CREATED)
    
    -- Target Auditable Polymorphic Columns
    auditable_type VARCHAR(255) NOT NULL,
    auditable_id BIGINT UNSIGNED NOT NULL,
    
    -- Kolom JSON standar kompatibel MariaDB & MySQL
    old_values JSON NULL,
    new_values JSON NULL,
    metadata JSON NULL, -- Struktur minimum wajib: {"actor_snapshot": {}, "roles": [], "request_context": {}, "extra": {}}
    
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(512) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    -- Definisi Indeks Kinerja
    INDEX idx_audit_logs_request (request_id),
    INDEX idx_audit_logs_actor (actor_type, actor_id),
    INDEX idx_audit_logs_auditable (auditable_type, auditable_id),
    INDEX idx_audit_logs_event (event_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 5. Aliran Arsitektur Event-Driven (Event Driven Architecture)

Mengacu pada arsitektur dekapling **ADR-003**, kita mendesain aliran pencatatan log audit menggunakan sistem **Laravel Event, Listener, & Queue**. Aliran ini memastikan waktu respon API tetap instan (sub-10ms) meskipun pencatatan log audit membutuhkan penulisan disk atau pemrosesan payload JSON yang kompleks.

### Diagram Aliran Data

```
+--------------------+
|  Business Service  |  <- Ditrigger oleh Controller / API
+--------------------+
          |
          v (Dispatches)
+--------------------+
|    Domain Event    |  <- Contoh: StudentGraduatedEvent, AcademicYearActivatedEvent
+--------------------+
          |
          v (Handled By)
+--------------------+
|    AuditListener   |  <- Menangkap event dan mengekstrak payload
+--------------------+
          |
          v (Pushed to)
+--------------------+
|    Queue Worker    |  <- Asynchronous Job Queue (Database / Redis)
+--------------------+
          |
          v (Processed)
+--------------------+
|  StoreAuditLogJob  |  <- Menyimpan data secara persisten ke database
+--------------------+
          |
          v (Saves)
+-----------------------------------+
| Database Source of Truth: Table   |
|            audit_logs             |
+-----------------------------------+
```

### Klasifikasi Sinkron vs Asinkron (Sync vs Async Event Pipeline)

Demi performa maksimal, mayoritas log audit disalurkan secara **Asynchronous** menggunakan Queue. Namun, untuk beberapa kategori keamanan tingkat tinggi, pencatatan wajib dilakukan secara **Synchronous** untuk menjamin data log segera tertulis sebelum transaksi berakhir (mencegah manipulasi jika terjadi interupsi).

| Tipe Event | Mode Eksekusi | Alasan Arsitektural |
|---|---|---|
| **Aktivitas CRUD Standar** (`student_created`, `guardian_updated`, dll) | **Asynchronous** | Performa respons API adalah prioritas utama. Penulisan log yang tertunda beberapa milidetik di latar belakang tidak berdampak negatif pada integritas data. |
| **Autentikasi Gagal** (`login_failed`) | **Synchronous** | Keamanan (Security Hardening). Log kegagalan login harus tercatat instan agar sistem pendeteksi Brute Force atau Intrusion Detection System (IDS) dapat merespon secara real-time tanpa risiko hilangnya log akibat antrean tertunda. |
| **Aktivitas Siklus Hidup Kritis** (`academic_year_activated`, `student_graduated`) | **Synchronous / Sync-on-Commit** | Integritas Tinggi. Aktivitas pengaktifan atau kelulusan melibatkan perubahan status hukum dan konfigurasi global yang tidak boleh terputus atau gagal dicatat saat transaksi database dijalankan. |

---

## 6. Kebijakan Retensi Data (Retention Policy)

Untuk menjaga ukuran database tetap terkontrol tanpa melanggar aspek kepatuhan hukum, dirumuskan Kebijakan Retensi Log Audit sebagai berikut:

1.  **Imutabilitas Log**: Record di dalam tabel `audit_logs` bersifat **Read-Only**. Tidak boleh ada endpoint API, controller, maupun service aplikasi yang memiliki kemampuan untuk memperbarui (Update) atau menghapus (Delete) record log audit secara langsung.
2.  **Rentang Waktu Penyimpanan (Retention Windows)**:
    -   **Hot Storage (Database Utama / MariaDB)**: Log disimpan selama **2 (dua) tahun**. Log dalam rentang waktu ini dapat diakses secara langsung dan instan oleh Admin melalui sistem monitoring internal.
    -   **Cold Storage (Cloud Storage / Compressed Archives)**: Log berumur **2 s.d 7 tahun** akan dipindahkan (archived) ke media penyimpanan eksternal yang murah dan aman (misalnya AWS S3 Glacier, Google Cloud Storage Archives, atau tabel terkompresi BigQuery) dalam format JSON/CSV bulanan. Setelah berhasil dipindahkan ke Cold Storage, data di database utama dapat dihapus (purged) melalui scheduled command bulanan.
    -   **Pemusnahan Data**: Setelah **7 (tujuh) tahun**, log audit dapat dimusnahkan secara permanen, sesuai dengan regulasi standar industri mengenai masa kedaluwarsa dokumen transaksional keuangan & akademik.

---

## 7. Forensic Requirements: Menjaga Keterbacaan & Snapshotting

Tantangan utama dari forensic logging pada database relasional adalah risiko kehilangan relasi data saat entitas dihapus atau mengalami perubahan struktural secara ekstrem.

### Strategi Forensik SIAM untuk Mengatasi Skenario Ekstrem:

#### A. Skenario: Pengguna (User/Aktor) Dihapus Permanen
*   **Masalah**: Jika relasi asing `user_id` bernilai `null` karena foreign key constraint, kita tidak mengetahui siapa pelaku aksi tersebut dari log.
*   **Strategi Forensik (Snapshotting)**:
    -   Pada saat event audit ditrigger, listener wajib mengambil snapshot profil dari aktor aktif, seperti: `user_name`, `user_email`, dan `user_roles`.
    -   Snapshot ini disimpan secara redundan di dalam kolom `metadata` sebagai JSON.
    -   *Hasil*: Meskipun baris data pengguna terkait telah dihapus secara fisik dari tabel `users`, log audit tetap menampilkan pelaku asli dengan jelas: `"actor_snapshot": {"email": "operator.tu@madrasah.sch.id", "name": "Ahmad Dani", "roles": ["Tata Usaha"]}`.

#### B. Skenario: Struktur Kelas, Siswa, atau Wali Dihapus (Siswa Lulus/Mutasi)
*   **Masalah**: Referensi `class_id` atau `guardian_id` pada model siswa yang dicatat di masa lalu menjadi tidak valid jika kelas atau wali dihapus dari database.
*   **Strategi Forensik (Polymorphic JSON Data)**:
    -   Kolom `old_values` dan `new_values` harus menyimpan state lengkap (full state representation) dari objek pada saat event terjadi, bukan sekadar ID relasi.
    -   *Hasil*: Jika siswa dimutasi, log audit untuk event `student_transferred` akan menyimpan seluruh state profil siswa pada hari mutasi tersebut, termasuk snapshot nama wali, nomor telepon wali, dan nama kelas terakhir di dalam field `metadata`.

#### C. Skenario: Perubahan Peran Pengguna (Role Mutation)
*   **Masalah**: Pengguna yang awalnya memiliki peran `Tata Usaha` diubah perannya menjadi `Wali Kelas`. Log audit historis tidak boleh menampilkan aktivitas masa lalunya sebagai `Wali Kelas`.
*   **Strategi Forensik (Temporal Role Capture)**:
    -   Setiap pencatatan log audit wajib menyertakan list role aktif aktor pada saat detik transaksi itu dieksekusi. Perubahan role di masa depan tidak akan memengaruhi data role historis yang sudah tertulis di dalam `metadata.actor_roles`.

---

## 8. Sprint 1G Backlog (Prioritas Implementasi)

Berikut adalah daftar tugas terurut untuk eksekusi implementasi sistem Audit Trail SIAM:

### 1. **Must Have (Kebutuhan Dasar & Keamanan)**
- [ ] Membuat berkas migrasi penyesuaian (alter/create) tabel `audit_logs` untuk mengimplementasikan skema polymorphic actor dan kolom `metadata` (JSON). *(Catatan: Jika di masa depan SIAM berpindah ke UUID Primary Key, relasi polimorfik ini harus dimigrasikan menggunakan `uuidMorphs()`)*.
- [ ] Membuat `AuditEvent Enum Registry` (misal: `AuditEvent::STUDENT_CREATED`, `AuditEvent::LOGIN_FAILED`) sebagai Single Source of Truth penamaan event audit untuk mencegah inkonsistensi string typo (seperti `student_create` vs `student-created`).
- [ ] Merancang kelas dasar Event Audit (`BaseAuditEvent`) yang mengemas data Request ID, IP Address, User Agent, dan Actor Snapshot secara otomatis dari konteks request aktif.
- [ ] Membuat middleware `RequestCorrelationMiddleware` yang bertugas men-generate UUID untuk setiap request, menyimpannya ke request context, mengirimkannya ke audit log (sebagai `request_id`), serta menambahkannya ke response header (misal `X-Request-ID`).
- [ ] Mengimplementasikan Trait `HasAuditLogs` versi baru yang mendukung pemetaan event dinamis ke format `event_name` katalog forensik menggunakan ENUM yang telah dibuat.
- [ ] Menerapkan pencatatan otomatis (auto-triggering) untuk aktivitas pembuatan, pembaruan, dan penghapusan (CRUD) di domain `Student`, `Guardian`, `AcademicYear`, `Semester`, dan `AcademicClass`.

### 2. **Should Have (Penyempurnaan Arsitektur & Performa)**
- [ ] Mematangkan integrasi Laravel Queue (Job `ProcessAuditLog`) untuk memproses penyimpanan log secara asinkron di latar belakang.
- [ ] Menambahkan penanganan event autentikasi secara khusus: `login_success` (Listener pada `Illuminate\Auth\Events\Login`), `login_failed` (Listener pada `Illuminate\Auth\Events\Failed`), dan `logout` (Listener pada `Illuminate\Auth\Events\Logout`).
- [ ] Mengimplementasikan filter penyaringan kolom sensitif (seperti `password`, `remember_token`, `pin`) agar tidak pernah bocor ke kolom `old_values` atau `new_values`.

### 3. **Nice To Have (Analitik & Monitoring)**
- [ ] Membuat command scheduler harian/bulanan untuk melakukan backup otomatis log di atas 2 tahun ke format CSV terkompresi (simulasi pemindahan ke Cold Storage).
- [ ] Menyediakan endpoint internal API khusus Administrator untuk mengambil daftar log audit dengan dukungan pencarian berdasarkan `request_id`, `actor_id`, dan filter tingkat keparahan `severity`.
- [ ] Mengonfigurasi notifikasi otomatis (misal Slack Webhook / Email Alerts) apabila terjadi event dengan severity `critical` atau anomali kegagalan autentikasi berulang (`login_failed` beruntun).

---

## 9. Keputusan Akhir Arsitektur: GO / NO-GO

Berdasarkan analisis kesenjangan arsitektur, kebutuhan madrasah akan pelacakan data siswa secara akurat, dan kepatuhan sistem SIAM terhadap standar tata kelola teknologi informasi:

### Keputusan: **GO**

#### Justifikasi:
1.  **Kesiapan Fondasi**: Basis kode SIAM saat ini sudah memiliki pondasi modular monolith yang sehat dan struktur database yang bersih. Kode-kode model akademik dan siswa telah siap menerima integrasi tanpa merusak business rules yang sudah ada.
2.  **Pentingnya Transparansi**: Perubahan status siswa (seperti pelulusan, skorsing, dan mutasi) adalah transaksi hukum akademik yang sangat sensitif. Tanpa audit trail forensik yang matang (dilengkapi snapshotting), madrasah berisiko menghadapi sengketa data akibat manipulasi tak terlacak oleh oknum operator.
3.  **Dukungan Skalabilitas**: Dengan pendekatan asinkron (Event-Driven Queue), implementasi ini dipastikan tidak akan mendegradasi performa operasional harian SIAM, sehingga aman digunakan untuk jangka panjang.

---
*Dokumen ini sah digunakan sebagai acuan pengembangan teknis sistem Audit Trail & Forensic Logging SIAM untuk Sprint 1G.*
