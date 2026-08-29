# Laporan Data Contract Audit - Sprint 2C.1A: Guardian

## 1. Tujuan
Memastikan seluruh kontrak data (Data Contract) untuk entitas `Guardian` tersinkronisasi secara konsisten dari tingkat Database (Migration) hingga ke Response (API/UI) sebelum pengembangan antarmuka (Blade UI) dilakukan. Ini bertujuan mencegah bug seperti *phantom fields*, *orphan fields*, atau inkonsistensi tipe data.

## 2. Database Contract (Migration)
Tabel: `guardians`
- `id` (bigint, unsigned, auto_increment, PK)
- `user_id` (bigint, unsigned, nullable, FK ke `users.id` ON DELETE SET NULL)
- `guardian_name` (varchar 150, not null)
- `guardian_relation` (enum: 'ayah', 'ibu', 'paman_bibi', 'kakek_nenek', 'lainnya', default: 'ayah')
- `phone_number` (varchar 20, not null)
- `address` (text, not null)
- `active_phone_number` (varchar 20, virtual, unique) -> Mencegah duplikasi nomor telepon untuk wali yang masih aktif (Soft Delete pattern).
- `created_at`, `updated_at`, `deleted_at`

**Status: VALID**

## 3. Model Contract (`App\Modules\Student\Models\Guardian`)
- **Fillable**: `user_id`, `guardian_name`, `guardian_relation`, `phone_number`, `address`
- **Casts**: `user_id` => 'integer'
- **Traits**: `SoftDeletes`, `HasAuditLogs`
- **Relations**: `user()` (BelongsTo), `students()` (HasMany)
- Tidak ada field yang tertinggal atau berlebih (phantom).

**Status: VALID**

## 4. Request Validation Contract (`StoreGuardianRequest` & `UpdateGuardianRequest`)
Aturan validasi mematuhi batasan database:
- `user_id`: `nullable|integer|exists:users,id`
- `guardian_name`: `required|string|max:150`
- `guardian_relation`: `required|string|in:ayah,ibu,paman_bibi,kakek_nenek,lainnya`
- `phone_number`: `required|string|max:20`
- `address`: `required|string`

**Status: VALID**

## 5. API / Resource Contract (`GuardianResource`)
Mapping response yang diekspos:
- `id`
- `user_id`
- `guardian_name`
- `guardian_relation`
- `phone_number`
- `address`
- `created_at` (ISO8601)
- `updated_at` (ISO8601)

**Status: VALID**

## 6. UI Contract (Persiapan View)
Form input yang perlu disiapkan pada Sprint 2C.2:
1. `user_id` (Select Option / Nullable)
2. `guardian_name` (Input Text, MaxLength 150)
3. `guardian_relation` (Select: Ayah, Ibu, Paman/Bibi, Kakek/Nenek, Lainnya)
4. `phone_number` (Input Text, MaxLength 20)
5. `address` (Textarea)

Semua kebutuhan input dapat dipetakan langsung ke struktur request dan fillable model.

## 7. Gap Analysis
- **Konsistensi Penamaan**: Seluruh field menggunakan format `snake_case` dari database hingga resource. Tidak ada camelCase yang tercampur.
- **Batasan (Constraints)**: Validasi di tingkat request sesuai dengan panjang kolom di database (`guardian_name` 150 karakter, `phone_number` 20 karakter).
- **Enum Safety**: `guardian_relation` yang diizinkan di database sama dengan rules validasi (`in:ayah,ibu,...`).
- **Orphan/Phantom**: Tidak ditemukan field liar yang tidak bisa disimpan ke dalam tabel.
- **Factory/Seeder**: Belum terdapat Factory spesifik untuk Guardian, namun untuk pengembangan UI tidak masalah karena data bisa dimasukkan secara manual dari UI/Postman.

## 8. PASS/FAIL Matrix

| Komponen | Pengecekan | Status |
|---|---|---|
| Migration | Tipe dan batas data didefinisikan | **PASS** |
| Model | Fillable & Relasi terdaftar | **PASS** |
| Request | Validasi sesuai schema DB | **PASS** |
| Resource | Response field lengkap | **PASS** |
| Service | Data logic mapping tepat | **PASS** |
| Repository | Pencarian spesifik relevan | **PASS** |

## 9. Kesimpulan: GO / NO-GO untuk Sprint 2C.2
Data Contract untuk modul Guardian sangat konsisten, bersih, dan sesuai standar yang ditetapkan dalam spesifikasi.

**Keputusan: GO** - Tim dapat segera melanjutkan ke Sprint 2C.2 untuk mulai membuat dan merender Blade View (index, create, edit, show) bagi modul Guardian.
