# Laporan Remediasi Backend - Sprint 2F.2: Academic Class

## 1. Tujuan
Menyelesaikan seluruh temuan dari audit kesiapan (Readiness Review) Sprint 2F.1 pada modul Academic Class sesuai dengan Architecture Decision Record (ADR-005) dan standar SIAM. Remediasi ini mempersiapkan fondasi *backend* yang aman, kokoh, dan terstandarisasi sebelum memasuki fase pengembangan antarmuka (Sprint 2F.3).

## 2. PASS/FAIL Matrix (Post-Remediation)

| Komponen | Status | Keterangan / Perubahan yang Dilakukan |
|---|---|---|
| **Migration & Schema** | **PASS** | Migration baru `2026_07_26_...` telah dibuat untuk mengubah *foreign key* ke `academic_year_id`, menambahkan `start_year` & `end_year` ke tabel `academic_years`, menambah `capacity`, `display_order`, serta mengimplementasikan `active_class_key` *virtual column* sesuai ADR-005. |
| **Model & Relationships** | **PASS** | `AcademicClass`: `$fillable` & `$casts` dimutakhirkan. Relasi diubah menjadi `academicYear()`. Accessor `full_name` dan _helper_ Romawi `getRomanGrade()` ditambahkan. `AcademicYear`: `$fillable` ditambah `start_year`, `end_year`. |
| **Request Validation** | **PASS** | `StoreAcademicClassRequest` dan `UpdateAcademicClassRequest` telah diperbarui dengan validasi `academic_year_id`, `capacity`, `display_order`, serta `Rule::unique` kustom untuk kombinasi `(academic_year_id, grade, name)` dengan `deleted_at IS NULL`. |
| **Service & Repository** | **PASS** | Fungsi `restore()` diimplementasikan pada lapis `BaseServiceInterface`, `BaseService`, dan ditimpa/dibungkus spesifik pada `AcademicClassService`. |
| **Controller & Routes** | **PASS** | `AcademicClassController` difaktorkan ulang menggunakan *Content Negotiation* (`wantsJson()`) sehingga siap melayani API dan Web UI. Route `create`, `edit`, `restore` ditambahkan di `web.php`. |
| **Policy & Permissions** | **PASS** | Permission `class.restore` ditambahkan ke `RolePermissionSeeder` dan didaftarkan pada `AcademicClassPolicy`. |
| **Audit Trail Compatibility**| **PASS** | Fitur Audit (lapis `BaseService` & trait `HasAuditLogs`) tetap terjaga kompatibilitasnya. |
| **Soft Delete & Restore** | **PASS** | Alur *Soft Delete* dan *Restore* tersambung sepenuhnya dari rute web hingga *Service Layer*. |
| **Content Negotiation** | **PASS** | Controller kini dapat mengembalikan baik `JsonResponse` (untuk *mobile*/API) maupun `View`/`RedirectResponse` (untuk aplikasi *web* portal SIAM). |

## 3. Daftar Perubahan Utama
1. **Perubahan Relasi Induk**: Rombel kini melekat pada *Academic Year*, bukan *Semester*. Hal ini mengantisipasi duplikasi rombel yang tidak perlu antar semester ganjil/genap dan menyiapkan arsitektur *Student Class Enrollments* di masa mendatang.
2. **Implementasi ADR-005**: Kolom *virtual* komposit `active_class_key` dibuat berdasarkan kombinasi `academic_year_id`, `grade`, dan `name`, lalu dijaga dengan *unique index*. Fungsionalitas *Soft Deletes* bekerja aman tanpa tabrakan *unique constraints*.
3. **Peningkatan Fungsionalitas Tahun Ajaran**: Modul `AcademicYear` ditambahkan atribut `start_year` dan `end_year` untuk mempermudah operasi pelaporan atau filter numerik di masa depan.
4. **Sentralisasi Penamaan Romawi**: Logika konversi tingkat kelas (7, 8, 9) ke Romawi (VII, VIII, IX) disentralisasi pada _helper_ `getRomanGrade()` di model `AcademicClass` dan terekspos via *Accessor* `full_name`.

## 4. Risiko Tersisa (Remaining Risks)
*   **Wali Kelas (Homeroom Teacher)**: Sesuai _Architecture Lock_, kolom ini berstatus *DEFERRED* hingga modul Guru/User Pegawai tersedia. Ketika modul tersebut dikerjakan, diperlukan migrasi lanjutan pada tabel `classes`.
*   **Student Enrollment**: Saat ini struktur memisahkan eksistensi *Class* (per tahun) dan penempatan (per semester). Namun tabel `student_class_enrollments` belum dibuat, yang berarti penempatan siswa baru bisa dilakukan setelah arsitektur modul *Enrollment* dikonseptualisasikan di *sprint* selanjutnya.

## 5. Keputusan Selanjutnya
**GO** untuk melanjutkan ke **Sprint 2F.3 (Academic Class UI Development)**. Fondasi *backend* secara fungsional telah siap menyajikan data rombel dan menerima asupan data yang terjamin integritasnya.
