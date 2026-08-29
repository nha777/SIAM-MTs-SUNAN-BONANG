# Laporan Readiness Review - Sprint 2C: Guardian Management UI

## 1. Tujuan
Melakukan audit dan verifikasi terhadap arsitektur backend modul Guardian (Wali Murid) sebelum melakukan pengembangan User Interface (UI) pada Sprint 2C. Hal ini untuk memastikan tidak ada inkonsistensi dengan standar *Database Freeze v1* dan arsitektur web yang digunakan.

## 2. Entity Relationship Review
- **Student ↔ Guardian**: 
  - `students` tabel memiliki `guardian_id` (Foreign Key ke `guardians.id`, `ON DELETE RESTRICT`).
  - Model `Student` memiliki relasi `belongsTo(Guardian::class)`.
  - Model `Guardian` memiliki relasi `hasMany(Student::class, 'guardian_id')`.
  - Fisik database sesuai. Karena menggunakan `SoftDeletes`, pembatasan RESTRICT hanya berlaku pada *hard delete*.
- **Guardian ↔ User**:
  - `guardians` memiliki `user_id` (Foreign Key ke `users.id`, `ON DELETE SET NULL`).
  - Model `Guardian` memiliki relasi `belongsTo(User::class)`.
- **Status: PASS**

## 3. RBAC Review
- Policy `GuardianPolicy` telah menggunakan *Spatie Permission* yang tepat:
  - `viewAny` & `view` → `guardian.view`
  - `create` → `guardian.create`
  - `update` → `guardian.update`
  - `delete` → `guardian.delete`
- **Temuan**: Policy untuk operasi `restore` belum didefinisikan dalam `GuardianPolicy`, padahal tabel `guardians` mendukung *soft delete*.
- **Status: CONDITIONAL PASS** (Membutuhkan penambahan `restore` policy jika fitur restore diperlukan).

## 4. API & Controller Review
- **Controller Saat Ini** (`GuardianController`):
  - Memiliki operasi `index`, `store`, `show`, `update`, dan `destroy`.
  - Masih dikunci secara ketat untuk merespon dengan `JsonResponse`.
- **Temuan/Gap**:
  - Belum mengimplementasikan **Content Negotiation** (pengecekan `$request->wantsJson()`) sehingga tidak dapat merender Blade View (akan merusak akses web browser).
  - Belum memiliki metode `create()` dan `edit()` untuk menampilkan form HTML.
  - Belum ada metode `restore()`.
- **Status: FAIL** (Membutuhkan refactoring pada `GuardianController`).

## 5. Validation Review
- `StoreGuardianRequest` dan `UpdateGuardianRequest` telah mendefinisikan aturan yang sesuai dengan tipe data di migrasi:
  - `user_id` (nullable, exists:users)
  - `guardian_name` (required, string, max:150)
  - `guardian_relation` (required, in:ayah,ibu,paman_bibi,kakek_nenek,lainnya)
  - `phone_number` (required, string, max:20)
  - `address` (required, string)
- **Status: PASS**

## 6. Audit Trail Compatibility Review
- Model `Guardian` menggunakan trait `HasAuditLogs`. Perekaman riwayat perubahan sudah didukung sepenuhnya secara otomatis.
- **Status: PASS**

## 7. UI Requirements Review & Gap Analysis
Berdasarkan audit di atas, untuk membangun UI Guardian pada Sprint 2C, ada sejumlah **Gap** yang harus diselesaikan di layer backend terlebih dahulu (Remediation):

1. **Routing Web**: Rute `create` dan `edit` perlu ditambahkan di `app/Modules/Student/Routes/web.php` untuk modul Guardian.
2. **Controller Refactoring**: `GuardianController` harus dimodifikasi untuk mendukung respons Blade View, dengan tetap mempertahankan respons JSON jika diminta oleh API (seperti pada `StudentController`). Metode `create` dan `edit` perlu dibuat.
3. **Paginasi & Pencarian**: Fungsi `index` di `GuardianController` harus diperbarui untuk mendukung pagination `paginate(10)` dan pencarian (`search`), agar UI daftar wali murid dapat menampilkannya dalam tabel.
4. **Soft Delete / Restore**:
   - UI perlu tombol pulihkan (Restore) bagi wali murid yang terhapus (inactive).
   - Membutuhkan penambahan fungsi `restore` di `GuardianService`, `GuardianController`, rute `web.php`, serta policy di `GuardianPolicy`.
   - **Peringatan Business Logic**: Di dalam `GuardianService::remove()`, penghapusan wali murid akan men-trigger *cascade deactivation* di mana status seluruh siswa di bawahnya diubah menjadi `keluar`. Jika terdapat fitur `restore`, mekanisme pemulihan status siswanya (jika ada) perlu dipertimbangkan, atau sekurang-kurangnya hanya me-restore data wali saja.

## 8. PASS/FAIL Matrix

| Komponen | Status | Catatan / Action Required |
|----------|--------|---------------------------|
| Database & Entity | **PASS** | Sesuai rancangan SIAM v1. |
| Validation & Audit | **PASS** | Aturan validasi dan trait audit sudah terpasang. |
| RBAC (Policy) | **CONDITIONAL PASS**| Kurang policy `restore`. |
| API & Controller | **FAIL** | Wajib *remediation*: Content negotiation, penambahan method web (create, edit), dan logic pagination/search. |
| Routes | **FAIL** | Wajib *remediation*: Tambah rute web untuk create dan edit. |

## 9. Rekomendasi Tindakan Selanjutnya (Pre-Sprint 2C)
Lakukan **Sprint 2B Remediation** untuk modul Guardian:
1. Update `GuardianController` agar menggunakan logic `wantsJson()` dan pagination (mirip `StudentController`).
2. Tambahkan `create`, `edit` routes.
3. Setelah *remediation* ini selesai, barulah desain dan pembuatan komponen Blade UI untuk Guardian (`index`, `form`, `create`, `edit`, `show`) dapat dimulai.
