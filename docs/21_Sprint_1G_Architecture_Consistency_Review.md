# Architecture Consistency Review
## Sprint 1G – Database Freeze v1 Compliance Audit

## 1. Pendahuluan
Dokumen ini disusun untuk meninjau konsistensi arsitektur dan kepatuhan rancangan teknis **Sprint 1G – Audit Trail & Forensic Logging Design** terhadap keputusan teknologi yang telah dibekukan secara resmi pada proyek **SIAM (Sistem Informasi Akademik Madrasah)**:
- **Database Engine**: MariaDB 10.x+ / MySQL 8.0+
- **Framework**: Laravel 12
- **Database Freeze**: Database Freeze v1

Tujuan audit ini adalah mengidentifikasi komponen rancangan SQL, tipe data, dan strategi indeks khusus PostgreSQL yang tidak sengaja tercantum dalam dokumen desain awal (`/docs/20_Sprint_1G_Audit_Trail_Forensic_Logging_Design.md`), mengevaluasi dampak teknisnya, serta merumuskan rekomendasi koreksi yang sepenuhnya kompatibel dengan MariaDB, MySQL, dan Laravel 12 Schema Builder.

---

## 2. Daftar Inkonsistensi yang Ditemukan (Gap Analysis)

Berdasarkan analisis menyeluruh terhadap rancangan tabel `audit_logs` pada dokumen desain Sprint 1G sebelumnya, ditemukan beberapa sintaks dan tipe data spesifik PostgreSQL:

1.  **Penggunaan Tipe Data `BIGSERIAL`**
    *   *Kutipan Desain*: `id BIGSERIAL PRIMARY KEY`
    *   *Analisis*: `BIGSERIAL` adalah tipe data auto-incrementing 8-byte pseudo-type khusus milik PostgreSQL. MariaDB dan MySQL tidak mengenali tipe data ini.

2.  **Penggunaan Tipe Data `JSONB`**
    *   *Kutipan Desain*: `old_values JSONB NULL`, `new_values JSONB NULL`, `metadata JSONB NULL`
    *   *Analisis*: `JSONB` adalah format penyimpanan JSON biner terkompresi dan terindeks khusus PostgreSQL. MariaDB menyimpan kolom JSON sebagai `LONGTEXT` dengan constraint pemeriksaan validitas JSON, sedangkan MySQL 8+ menggunakan format biner internal sendiri namun dideklarasikan dengan tipe data `JSON`, bukan `JSONB`.

3.  **Penggunaan `TIMESTAMP(0) WITHOUT TIME ZONE`**
    *   *Kutipan Desain*: `created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP`
    *   *Analisis*: Klausa `WITHOUT TIME ZONE` merupakan dialek SQL spesifik PostgreSQL untuk mendefinisikan timestamp tanpa offset zona waktu. MariaDB dan MySQL tidak mendukung sintaks eksplisit ini.

4.  **Sintaks Pembuatan Indeks Terpisah**
    *   *Kutipan Desain*: Penggunaan penamaan indeks manual setelah pembuatan tabel (contoh: `CREATE INDEX idx_audit_logs_actor ON audit_logs(actor_type, actor_id);`). Walaupun valid di SQL umum, sintaks ini tidak memanfaatkan kemudahan abstraksi Laravel Schema Builder.

---

## 3. Dampak Teknis Jika Tetap Menggunakan PostgreSQL Syntax pada MariaDB/MySQL

Jika rancangan DDL (Data Definition Language) dari dokumen desain awal dipaksakan untuk dijalankan di lingkungan MariaDB/MySQL (baik melalui migrasi Laravel mentah atau query langsung), maka akan terjadi kegagalan sistem sebagai berikut:

*   **Syntax Error (Fatal Crash)**: Eksekusi migrasi akan langsung gagal (*crash*) saat parser SQL MariaDB menemui kata kunci `BIGSERIAL` dan `WITHOUT TIME ZONE`, menyebabkan proses deploy atau migrasi terhenti sepenuhnya.
*   **Ketidakkompatibilitas Driver**: Driver database Laravel (PDO MySQL) akan melemparkan exception ketika mencoba memetakan kolom bertipe `JSONB` karena tipe tersebut tidak dideklarasikan di bawah standar tipe data MariaDB/MySQL.
*   **Penurunan Efisiensi Penyimpanan**: MariaDB mengimplementasikan penyimpanan data JSON menggunakan tipe data `LONGTEXT` di balik layar. Memaksakan sintaks `JSONB` akan merusak portabilitas skema jika developer mencoba melakukan replikasi data ke node MariaDB sekunder.

---

## 4. Rekomendasi Koreksi Kompatibilitas (MariaDB / MySQL / Laravel 12)

Untuk memastikan kepatuhan 100% terhadap **Database Freeze v1**, semua definisi database harus diatur menggunakan abstraksi tingkat tinggi dari **Laravel 12 Schema Builder**:

1.  **Koreksi `BIGSERIAL`**:
    *   Gunakan `$table->id()` pada Laravel Schema Builder. Ini secara otomatis memetakan ke tipe data `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY` yang merupakan standar optimal untuk MariaDB dan MySQL.
2.  **Koreksi `JSONB`**:
    *   Gunakan `$table->json('column_name')`. Laravel secara cerdas akan menerjemahkannya menjadi tipe data `JSON` di MySQL 8+ atau `LONGTEXT` dengan constraint pengecekan `JSON_VALID()` di MariaDB.
3.  **Koreksi `TIMESTAMP`**:
    *   Gunakan `$table->timestamp('created_at')->useCurrent()` atau `$table->useCurrent()`. Di MariaDB/MySQL, ini akan diterjemahkan menjadi tipe data `TIMESTAMP` standar yang sangat efisien (4-byte) untuk penanda waktu pembuatan log.
4.  **Koreksi Indeks**:
    *   Gunakan metode fluen milik Laravel Schema Builder seperti `$table->index(['actor_type', 'actor_id'])` langsung di dalam definisi migrasi, sehingga penamaan indeks diatur secara otomatis dan portabel di seluruh driver SQL.

---

## 5. Versi Revisi DDL Tabel `audit_logs` (Kompatibel 100% dengan MariaDB & MySQL)

### A. Raw SQL DDL (Standard MariaDB 10.x+ / MySQL 8+)
Jika dijalankan secara manual pada konsol database MariaDB/MySQL:

```sql
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id CHAR(36) NOT NULL UNIQUE, -- Menggunakan representasi string UUID standar
    request_id CHAR(36) NOT NULL,
    severity VARCHAR(20) NOT NULL DEFAULT 'info',
    
    -- Actor Polymorphic Columns (Nullable untuk System/Guest events)
    actor_id BIGINT UNSIGNED NULL,
    actor_type VARCHAR(255) NULL,
    
    event_name VARCHAR(100) NOT NULL, -- Diisi dari ENUM internal
    
    -- Target Auditable Polymorphic Columns
    auditable_type VARCHAR(255) NOT NULL,
    auditable_id BIGINT UNSIGNED NOT NULL,
    
    -- Penyimpanan JSON standar MariaDB/MySQL
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

### B. Blueprint Migrasi Laravel 12 (PHP)
Skema migrasi Laravel 12 yang bersih, portabel, dan aman dari vendor-lockin database:

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->uuid('event_id')->unique();
            $table->uuid('request_id')->index();
            $table->string('severity', 20)->default('info');
            
            // Polymorphic Actor (Catatan: Gunakan nullableUuidMorphs('actor') di masa depan jika migrasi ke UUID Primary Key)
            $table->nullableMorphs('actor'); // Menghasilkan actor_id (BIGINT UNSIGNED) dan actor_type (VARCHAR)
            
            $table->string('event_name', 100)->index(); // Diisi dari ENUM internal
            
            // Polymorphic Target (Auditable) (Catatan: Gunakan uuidMorphs('auditable') di masa depan jika migrasi ke UUID Primary Key)
            $table->morphs('auditable'); // Menghasilkan auditable_id (BIGINT UNSIGNED) dan auditable_type (VARCHAR)
            
            // Kolom JSON Portabel
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable(); // Struktur minimum wajib: {"actor_snapshot": {}, "roles": [], "request_context": {}, "extra": {}}
            
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
```

---

## 6. Hasil Audit & Keputusan Akhir

Evaluasi kepatuhan dokumen arsitektur terhadap regulasi pembekuan teknologi database pada proyek SIAM menghasilkan keputusan berikut:

### Keputusan: **PASS dengan Syarat Koreksi (Conditional PASS)**

Dengan catatan bahwa seluruh tim arsitek dan pengembang wajib memperbarui rujukan desain teknis dari format dialect PostgreSQL ke skema portabel MySQL/MariaDB yang dijabarkan dalam laporan review ini. Penggunaan repositori dan database transaksional untuk fitur audit trail di Sprint 1G secara resmi diizinkan menggunakan MariaDB 10.x+ sebagai *Source of Truth*.

---
*Laporan Audit Konsistensi ini disahkan untuk direferensikan dalam proses penyempurnaan implementasi Sprint 1G.*
