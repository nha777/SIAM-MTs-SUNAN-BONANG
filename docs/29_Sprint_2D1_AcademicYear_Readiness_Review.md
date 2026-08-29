# Laporan Audit Kesiapan UI - Sprint 2D.1: Academic Year

## 1. Tujuan
Melakukan audit menyeluruh terhadap modul Academic Year (Backend) sebelum memulai pengembangan antarmuka (UI) pada Sprint 2D. Tujuannya adalah memverifikasi bahwa semua service, contract, permission, dan business logic siap digunakan untuk operasi CRUD dan penanganan state keaktifan.

## 2. PASS/FAIL Matrix

| Komponen | Status | Keterangan |
|---|---|---|
| **Migration & Schema** | **PASS** | Skema unik `active_name` (Virtual Column) sudah diimplementasikan dengan baik untuk kombinasi soft-delete dan unique constraints. |
| **Model** | **PASS** | Traits `SoftDeletes` dan `HasAuditLogs` telah ditambahkan. |
| **Request Validation** | **FAIL** | Tidak mencegah input `is_active` dari form langsung, yang dapat bypass business logic (single active). |
| **Service & Repository** | **FAIL** | 1. Metode `activate` menggunakan mass-update untuk `deactivateAll`, sehingga mem-bypass model event (Audit Trail tidak tercatat untuk yang non-aktif).<br>2. Method `restore` belum diimplementasikan di Service.<br>3. `store` & `update` (turunan BaseService) tidak men-deaktivasi tahun ajaran lain jika request membawa `is_active=1`. |
| **Controller & Routes** | **FAIL** | Endpoint/method `destroy` (Soft Delete) dan `restore` sama sekali belum ada. |
| **Policy & Permissions** | **FAIL** | Permission dan rule untuk `update`, `delete`, dan `restore` belum ada (RolePermissionSeeder dan AcademicYearPolicy). |

## 3. Gap Analysis & Temuan Masalah

1. **Dual-Active Concurrency Risk dari Store/Update:**
   Karena `store` dan `update` diwarisi dari `BaseService` tanpa men-override logika penyimpanannya, jika ada request create/edit dengan payload `is_active: true`, data akan langsung tersimpan tanpa men-deaktivasi tahun ajaran lain (bypass logika yang ada di method `activate`).
2. **Bypass Audit Trail pada Deaktivasi:**
   Metode `deactivateAll()` pada `AcademicYearRepository` melakukan mass update query (`$this->model->query()->update(...)`). Mass update mem-bypass model event, sehingga data yang menjadi non-aktif tidak akan memicu trait `HasAuditLogs` (Audit Trail tidak tercatat).
3. **Absennya Soft Delete & Restore:**
   Fitur Soft Delete (`destroy`) dan `restore` belum di-expose di level Route maupun Controller. Service juga belum mendaftarkan fungsi `restore`.
4. **Inkonsistensi RBAC:**
   Berbeda dengan entitas Student dan Guardian yang lengkap (mencakup `update`, `delete`, `restore`), model `AcademicYear` hanya memiliki `academic_year.view`, `academic_year.create`, dan `academic_year.activate` pada Seeder dan Policy. Pada method `AcademicYearController::update()`, authorisasinya dipaksa memanggil `@can('view')`.

## 4. Remediation List (Action Items)

Sebelum melangkah ke implementasi UI (Blade Components), backend harus diperbaiki terlebih dahulu (Sprint 2D.2 Backend Remediation):

- **[ ] Action 1:** Update `RolePermissionSeeder` untuk menambahkan permission `academic_year.update`, `academic_year.delete`, dan `academic_year.restore`.
- **[ ] Action 2:** Lengkapi `AcademicYearPolicy` dengan metode `update`, `delete`, dan `restore`. Sesuaikan pemanggilan Gate di Controller.
- **[ ] Action 3:** Tambahkan route dan controller method untuk `destroy` (Soft Delete) dan `restore` pada modul Academic Year.
- **[ ] Action 4:** Tambahkan method `restore()` pada `AcademicYearService` (karena tidak tersedia secara bawaan di `BaseService`).
- **[ ] Action 5:** Lakukan override metode `store()` dan `update()` di `AcademicYearService`. Apabila payload mengandung `is_active = true` (atau `1`), service harus memanggil `deactivateAll()` di dalam satu database transaction.
- **[ ] Action 6:** Ubah `deactivateAll()` pada Repository (opsional/disarankan) agar melakukan iterasi dan menyimpan model satu per satu (untuk `getActiveAcademicYear()`) jika Audit Trail sangat wajib mencatat event "deactivated".

## 5. Keputusan (GO / NO-GO)
**NO-GO** untuk UI Development saat ini. Backend Academic Year belum siap memenuhi standar bisnis, keamanan (RBAC), dan operasional (Soft Delete) secara konsisten. Harus dilakukan perbaikan (Remediation) terlebih dahulu sebelum membuat antarmuka penggunanya.
