# Laporan Audit Kesiapan UI - Sprint 2E.1: Semester

## 1. Tujuan
Melakukan audit menyeluruh terhadap modul Semester (Backend) sebelum memulai pengembangan antarmuka (UI) pada Sprint 2E. Fokus audit mencakup verifikasi skema database, RBAC, *business logic* (single active semester), Audit Trail, serta konsistensi arsitektur dengan standar SIAM.

## 2. PASS/FAIL Matrix

| Komponen | Status | Keterangan |
|---|---|---|
| **Migration & Schema** | **PASS** | Skema virtual column `active_semester` mencegah duplikasi data aktif (academic_year_id + semester) dengan sangat baik. |
| **Model** | **PASS** | Relasi `BelongsTo` (AcademicYear), trait `SoftDeletes`, dan `HasAuditLogs` sudah diimplementasikan. |
| **Request Validation** | **FAIL** | Tidak mencegah payload `is_active` dari form langsung, berpotensi mem-bypass business logic aktivasi. |
| **Service & Repository** | **FAIL** | 1. Metode `deactivateAll()` pada Repo mem-bypass Audit Trail (mass-update).<br>2. `store` & `update` (Service) tidak men-deaktivasi semester lain jika payload `is_active=1` (Dual-active risk).<br>3. Method `restore` belum diimplementasikan. |
| **Controller & Routes** | **FAIL** | 1. Masih API-only (tidak menangani content negotiation `wantsJson()`).<br>2. Endpoint `create` dan `edit` untuk UI Blade tidak ada di `web.php`.<br>3. Method/route `destroy` (Soft Delete) dan `restore` belum ada.<br>4. Otorisasi salah pada `update` (memanggil `@can('view')`). |
| **Policy & Permissions** | **FAIL** | 1. Permission `semester.update`, `semester.delete`, `semester.restore` belum terdaftar di Seeder dan Policy.<br>2. Role "Tata Usaha" sama sekali belum diberi permission untuk mengelola modul Semester. |

## 3. Gap Analysis & Temuan Masalah

1. **Dual-Active Semester Risk:** Sama seperti kasus pada *Academic Year*, metode `store` dan `update` diwariskan dari `BaseService` tanpa override logika. Jika request dikirim dengan `is_active=true`, sistem akan menyimpan data tanpa men-deaktivasi semester yang sedang aktif, merusak validitas data (dan bisa gagal secara DB constraint).
2. **Audit Bypass pada Deaktivasi:** Metode `SemesterRepository::deactivateAll()` melakukan query mass-update. Model event tidak akan di-_fire_, sehingga aktivitas penonaktifan semester sebelumnya tidak akan tercatat dalam tabel Audit Log.
3. **Absennya Operasi Operasional (Soft Delete & Restore):** Fitur soft delete sama sekali belum dapat diakses melalui Controller, Service, maupun Route.
4. **RBAC & Otorisasi Inkonsisten:**
   - Seeder tidak memiliki izin untuk `update`, `delete`, dan `restore` semester.
   - Kebijakan izin belum diberikan ke peran administratif standar (`Tata Usaha` seharusnya bisa Create, View, dan Update).
   - Method `SemesterController::update()` keliru memverifikasi pengguna dengan izin `view`.
5. **API-Only Controller:** Berbeda dengan modul `Student`, `Guardian`, dan `AcademicYear` yang sudah melayani JSON dan Web UI (Blade), `SemesterController` masih _hardcoded_ mengembalikan respons JSON.

## 4. Remediation List (Action Items)

Sebelum berlanjut ke Sprint 2E.3 (Pembuatan UI), backend **harus** melalui remediasi (Sprint 2E.2):

- **[ ] Action 1:** Update `RolePermissionSeeder` untuk melengkapi _permissions_ (`semester.update`, `semester.delete`, `semester.restore`).
- **[ ] Action 2:** Sinkronisasi *role* Tata Usaha agar mendapatkan izin operasional `semester.view`, `semester.create`, dan `semester.update`.
- **[ ] Action 3:** Lengkapi method `update`, `delete`, dan `restore` di `SemesterPolicy`. Perbaiki `Gate::authorize()` di `SemesterController`.
- **[ ] Action 4:** Override `store()` dan `update()` pada `SemesterService` untuk membungkus pengecekan `is_active` dan `deactivateAll` di dalam `DB::transaction`.
- **[ ] Action 5:** Ubah `deactivateAll()` di `SemesterRepository` agar melakukan _looping_ update satu per satu untuk men-_trigger_ Audit Logs.
- **[ ] Action 6:** Buat implementasi fungsi `restore()` pada Service dan Interface-nya.
- **[ ] Action 7:** Modifikasi `SemesterController` (tambah fungsi `create`, `edit`, `destroy`, `restore`) beserta validasi negosiasi konten (`$request->wantsJson()`).
- **[ ] Action 8:** Tambahkan Endpoint UI (GET `create`, GET `edit`) dan Endpoint logic (`destroy`, `restore`) di `web.php`.

## 5. Keputusan (GO / NO-GO)
**NO-GO** untuk UI Development.
Kondisi backend modul Semester identik dengan awal modul Academic Year yang mana penuh dengan risiko bypass keamanan, dual-active bugs, serta *routing* yang belum mendukung UI (API-only). Wajib dilakukan **Backend Remediation** terlebih dahulu.
