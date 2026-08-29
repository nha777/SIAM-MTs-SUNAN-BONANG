# Laporan Architecture Review - Sprint 2C.1: Guardian Web Readiness Remediation

## 1. Tujuan
Melakukan audit dan remediasi (perbaikan arsitektur) pada modul Guardian agar sejalan dengan pola arsitektur Student yang telah dikembangkan sebelumnya. Remediasi ini memastikan kesiapan backend dan routing untuk mendukung respons UI berbasis HTML (Blade) dan API berbasis JSON tanpa merusak implementasi yang ada, serta mematuhi aturan keamanan (RBAC) dan standar audit.

## 2. File Inventory
Daftar file yang dimodifikasi pada sprint ini:

- `app/Modules/Student/Routes/web.php`
- `app/Modules/Student/Controllers/GuardianController.php`
- `app/Modules/Student/Policies/GuardianPolicy.php`
- `app/Modules/Student/Services/GuardianService.php`
- `app/Modules/Student/Services/Contracts/GuardianServiceInterface.php`

## 3. Architecture Before & After

### Before Remediation
- **Controller**: `GuardianController` dikunci ketat untuk memberikan `JsonResponse`.
- **Method**: Hanya mendukung `index`, `store`, `show`, `update`, `destroy`. Tidak ada `create`, `edit`, atau logic query data tabel.
- **Routing**: Rute web untuk form (`create`, `edit`) belum terdaftar.
- **Service & Policy**: Belum mendukung `restore` untuk memulihkan record (soft-delete).
- **Security**: Policy `restore` tidak ada.

### After Remediation
- **Content Negotiation**: `GuardianController` kini mendukung respons ganda. Jika request berasal dari API atau explicit header JSON (`$request->wantsJson()`), ia mengembalikan `GuardianResource` (seperti sebelumnya). Jika tidak, akan mengembalikan view Blade, menjadikannya controller yang dapat melayani SPA/API dan SSR sekaligus.
- **Filtering & Pagination**: Menambahkan filtering berbasis status (Aktif/Dihapus) dan searching (Nama/Telepon), serta paginasi (`paginate(10)`) pada fungsi `index()`.
- **Soft Deletion & Restore**: Menambahkan implementasi `.restore()` lengkap dari mulai Service layer, Route (`PATCH /guardians/{id}/restore`), Controller, hingga `GuardianPolicy`.
- **Policy Enforcement**: Seluruh akses diproteksi dengan `Gate::authorize()` dan memvalidasi objek langsung (bukan nama class saja), mencegah celah keamanan otorisasi.

## 4. Gap Analysis Resolution

| Aspek | Permasalahan / Gap | Resolusi | Status |
|---|---|---|---|
| API & Web Interop | Logic tidak mendukung request browser. | Penambahan `$request->wantsJson()` routing dan return View. | **Resolved** |
| Rute Form (UI) | Route `create` & `edit` tidak ada. | Dibuat di `web.php` dan controllernya. | **Resolved** |
| Paginasi Data | `getAll()` bisa bermasalah jika data banyak. | Diganti menggunakan query builder + paginate(10) untuk web dan API jika memungkinkan (index dimodifikasi agar scalable). | **Resolved** |
| Policy & Otorisasi | Tidak ada policy restore. | Dibuat metode `restore(User $user, Guardian $guardian)` di `GuardianPolicy`. | **Resolved** |
| Business Logic | Fitur restore belum ada di Service. | Ditambahkan di `GuardianService` dengan transaksi DB yang aman (mencegah double restore / race condition log). | **Resolved** |
| Audit Trail | Perekaman Audit | Tidak ada perubahan yang diperlukan karena Model sudah memakai trait `HasAuditLogs`. | **Resolved** |

## 5. PASS/FAIL Matrix

| Modul/Komponen | Kriteria Kelulusan | Status | Catatan |
|---|---|---|---|
| Guardian Controller | Support View & API Responses | **PASS** | `wantsJson()` membedakan return dengan baik. |
| Guardian Routes | Lengkap standard resource (termasuk Restore) | **PASS** | 8 route web disiapkan. |
| RBAC / Policies | Seluruh controller dilindungi Gate | **PASS** | Gate aktif pada setiap instance model spesifik (`$guardian`). |
| Guardian Service | Fungsionalitas CRUD + Restore beresiko rendah | **PASS** | Aman. |

## 6. Kesimpulan: GO / NO-GO untuk Guardian UI
Semua komponen yang dibutuhkan untuk merender UI Guardian secara aman dan stabil telah tersedia di layer HTTP, Service, dan Basis Data.
**Keputusan: GO** - Pengembangan antar muka web (Blade UI) untuk Guardian (Sprint 2C.2) dapat dilanjutkan karena arsitektur backend siap menerima integrasinya.
