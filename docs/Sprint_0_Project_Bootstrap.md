# Sprint 0: Project Bootstrap - SIAM (Sistem Informasi Administrasi Madrasah)

Dokumen ini mendokumentasikan spesifikasi teknis, arsitektur dasar, dan rencana bootstrap untuk pengembangan **SIAM** menggunakan **Laravel 12 (PHP 8.3)** dengan arsitektur **Modular Monolith**.

---

## 1. Struktur Folder (Modular Monolith)

Mengikuti prinsip **ADR-001 (Modular Monolith)**, semua domain bisnis diisolasi di dalam direktori `app/Modules/`. Setiap modul bersifat mandiri (*self-contained*) dan memiliki lapisan *Controller*, *Service*, *Repository*, *Route*, dan *View* tersendiri.

Berikut adalah struktur direktori untuk fondasi SIAM (Fase Core & Auth):

```text
siam-root/
├── .github/
│   └── workflows/
│       └── ci.yml              # GitHub Actions untuk Laravel Pint, PHPStan, dan Tests
├── app/
│   ├── Console/
│   ├── Exceptions/
│   ├── Http/
│   │   └── Middleware/         # Global Middleware (RBAC, Audit Log, dll.)
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   └── ModuleServiceProvider.php  # Provider utama untuk mendaftarkan modul secara otomatis
│   └── Modules/                # Pusat modul bisnis (Modular Monolith)
│       ├── Base/               # Kelas abstrak & kontrak reusable
│       │   ├── Contracts/
│       │   │   ├── BaseRepositoryInterface.php
│       │   │   └── BaseServiceInterface.php
│       │   ├── Repositories/
│       │   │   └── BaseRepository.php
│       │   ├── Services/
│       │   │   └── BaseService.php
│       │   └── Traits/
│       │       └── HasAuditLogs.php
│       │
│       └── Auth/               # Modul Autentikasi & Pengguna (Skeleton)
│           ├── Controllers/
│           │   └── AuthController.php
│           ├── Models/
│           │   └── User.php
│           ├── Repositories/
│           │   ├── UserRepository.php
│           │   └── Contracts/
│           │       └── UserRepositoryInterface.php
│           ├── Services/
│           │   └── AuthService.php
│           ├── Routes/
│           │   ├── web.php
│           │   └── api.php
│           └── Views/
│
├── config/
├── database/
│   ├── migrations/            # Migrasi global & tabel sistem (users, audit_logs)
│   │   ├── 2026_07_16_000000_create_users_table.php
│   │   └── 2026_07_16_000001_create_audit_logs_table.php
│   └── seeders/               # Seeder sistem dasar (Roles, Permissions)
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── layouts/           # Template Blade Bootstrap 5 global
│       └── components/        # Blade Components global
├── routes/
│   ├── web.php                # Fallback / Routing global
│   └── api.php
├── tests/
│   ├── Architecture/          # Tes kepatuhan arsitektur (Pest Arch)
│   ├── Feature/               # Integration & HTTP tests
│   └── Unit/                  # Unit tests untuk Services & Repositories
├── composer.json              # Konfigurasi dependensi PHP & Static Analysis
├── phpstan.neon               # Konfigurasi PHPStan/Larastan
└── pint.json                  # Konfigurasi code-style Laravel Pint
```

---

## 2. Daftar Package Composer (PHP/Backend)

Untuk membangun fondasi yang kokoh pada **Laravel 12**, berikut adalah dependensi Composer yang didefinisikan dalam `composer.json`:

### Dependensi Produksi (`require`):
1. **`spatie/laravel-permission`**: Mengelola Role-Based Access Control (RBAC).

### Dependensi Pengembangan & Analisis (`require-dev`):
1. **`pestphp/pest`**: Framework testing modern untuk PHP.
2. **`pestphp/pest-plugin-laravel`**: Integrasi Pest dengan Laravel.
3. **`pestphp/pest-plugin-arch`**: Pengunci arsitektur Modular Monolith (Pest Arch).
4. **`larastan/larastan`**: Pembungkus PHPStan untuk analisis statis Laravel guna menjamin keamanan tipe (*type safety*).
5. **`laravel/pint`**: Alat bantu penyeragaman gaya penulisan kode (code-style formatter).
6. **`barryvdh/laravel-debugbar`**: Alat bantu profiling database selama fase development.

---

## 3. Daftar Package NPM (Frontend/Asset)

Setup frontend akan menggunakan Vite untuk manajemen aset Bootstrap 5:
1. **`bootstrap`**: CSS framework responsif versi 5.3.x.
2. **`@popperjs/core`**: Dependensi Bootstrap dropdown dan popover.
3. **`sass`**: Kompiler CSS untuk modifikasi variabel Bootstrap.
4. **`lucide` / `lucide-static`**: Set ikon modern untuk navigasi admin.

---

## 4. Strategi Modularisasi & Komunikasi Antar-Modul

* **Isolasi Domain**: Modul `Auth` mengelola datanya sendiri. Modul bisnis lain kelak tidak boleh mengakses tabel user secara mentah di luar Repository.
* **Abstraksi Database (ADR-002)**: Penyuntingan data wajib melewati `BaseRepository` untuk menjaga standarisasi kueri dan kemudahan *mocking* saat unit test.

---

## 5. Strategi RBAC (Role-Based Access Control)

Sistem menggunakan `spatie/laravel-permission` berbasis hak akses (*permissions*).
* **Super Admin**: Akses penuh sistem.
* **Bendahara**: Administrasi tagihan dan laporan keuangan.
* **Kepala Madrasah**: Read-only laporan dan audit log.
* **Orang Tua**: Melihat tagihan anak dan riwayat pembayaran.

---

## 6. Strategi Audit Log (Traceability)

Menerapkan skema audit log yang komprehensif dan *immutable* sesuai **BR-007** dengan perluasan forensik digital.

### Struktur Tabel `audit_logs` yang Diperluas:
* `id` (BIGINT, Primary Key)
* `event_id` (UUID, Unik): Pengenal unik untuk satu kejadian bisnis (misal: satu pembayaran menghasilkan beberapa log terhubung).
* `request_id` (UUID): Mengelompokkan semua log yang dipicu oleh satu HTTP request yang sama.
* `severity` (ENUM: 'info', 'warning', 'critical'): Menentukan tingkat urgensi log (e.g., 'critical' untuk penolakan transaksi atau kegagalan auth).
* `user_id` (BIGINT, Nullable): Pengguna yang melakukan aksi.
* `event` (VARCHAR): Jenis kejadian (`created`, `updated`, `deleted`, `failed_auth`, dll.).
* `auditable_type` / `auditable_id` (Polymorphic Relation).
* `old_values` (JSON, Nullable).
* `new_values` (JSON, Nullable).
* `ip_address` (VARCHAR).
* `user_agent` (VARCHAR).
* `created_at` (TIMESTAMP).

---

## 7. Strategi CI/CD (GitHub Actions)

Alur otomatisasi (`.github/workflows/ci.yml`) dikonfigurasi untuk menjalankan tiga tahap pemeriksaan pada setiap *Pull Request* dan *push* ke branch utama:
1. **Linting (Laravel Pint)**: Memastikan kerapian kode sesuai standar Laravel.
2. **Static Analysis (PHPStan/Larastan)**: Menangkap kesalahan logika, ketidakcocokan tipe parameter, dan kebocoran tipe sebelum runtime.
3. **Tests (Pest PHP)**: Menjalankan unit tests, feature tests, dan check kepatuhan arsitektur.
