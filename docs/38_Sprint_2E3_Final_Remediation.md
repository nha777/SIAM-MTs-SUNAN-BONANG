# Laporan Remediasi Akhir - Sprint 2E.3: Semester Management

## 1. Tujuan
Memperbaiki celah keamanan pencarian (OR Leakage) pada fitur filter `SemesterController@index`. Bug ini dapat membocorkan status data (contohnya data yang seharusnya di-soft delete muncul sebagai data aktif) apabila operator OR pada Query Builder tidak dibungkus dengan baik, sehingga bertabrakan dengan filter parameter `status`.

## 2. Temuan dan Analisis Masalah
**Lokasi**: `app/Modules/Academic/Controllers/SemesterController.php` (Fungsi `index`)

Pada awalnya, filter logika pencarian di-*build* seperti ini:
```php
if ($search) {
    $query->where('semester', 'like', "%{$search}%")
          ->orWhereHas('academicYear', function($q) use ($search) {
              $q->where('name', 'like', "%{$search}%");
          });
}
```

Implementasi di atas akan menghasilkan skrip SQL ekuivalen:
```sql
WHERE semester LIKE '%x%'
OR academic_year.name LIKE '%x%'
AND deleted_at IS NULL
```

Klausa `AND deleted_at IS NULL` yang ditambahkan otomatis (karena _SoftDeletes_) dan klausa status lain akan tercampur dengan klausa `OR`. Hal ini membuat hasil query dapat membocorkan baris data tanpa mempedulikan status filter di belakangnya selama `semester` cocok dengan `%x%`.

## 3. Resolusi Remediasi
Logika query di atas di-*refactor* agar _search statement_ dikelompokkan dalam satu kesatuan klausa kurung `()` (grouping).
```php
if ($search) {
    $query->where(function ($q) use ($search) {
        $q->where('semester', 'like', "%{$search}%")
          ->orWhereHas('academicYear', function($sub) use ($search) {
              $sub->where('name', 'like', "%{$search}%");
          });
    });
}
```

Pembaruan ini menghasilkan ekuivalen SQL yang benar dan aman:
```sql
WHERE (
    semester LIKE '%x%'
    OR academic_year.name LIKE '%x%'
)
AND ...
```

## 4. Keputusan Akhir
- **Status Akhir:** **PASS**
- **Kesimpulan:** 
  Celah OR Leakage pada `SemesterController@index` berhasil diperbaiki. Pembungkusan *query* ini sejalan dengan remediasi yang sama yang diterapkan pada modul *Student* sebelumnya, sehingga menjaga konsistensi perbaikan keamanan di berbagai modul dalam platform. Semester UI Management secara keseluruhan kini telah solid, bebas *bug*, dan lolos Audit Penuh.
