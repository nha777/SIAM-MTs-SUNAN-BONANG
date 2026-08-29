# Laporan Verifikasi Model Eloquent (Model Verification Review) - SIAM

Dokumen ini mendokumentasikan hasil **Verifikasi Model Eloquent (Model Verification Review)** untuk memastikan kesesuaian 100% antara model fisik yang diimplementasikan pada **Sprint 1B** dengan spesifikasi arsitektur modular monolith pada **Database Freeze v1**.

---

## 1. Spesifikasi Model Hasil Implementasi

### A. Model `Guardian`
* **Namespace**: `App\Modules\Student\Models\Guardian`
* **Tabel**: `guardians`
* **Fitur & Trait**: `SoftDeletes`, `HasAuditLogs`
* **Relasi**:
  * `user()` $\rightarrow$ `BelongsTo` ke `User` (`user_id`)
  * `students()` $\rightarrow$ `HasMany` ke `Student` (`guardian_id`)

### B. Model `Student`
* **Namespace**: `App\Modules\Student\Models\Student`
* **Tabel**: `students`
* **Fitur & Trait**: `SoftDeletes`, `HasAuditLogs`
* **Relasi**:
  * `guardian()` $\rightarrow$ `BelongsTo` ke `Guardian` (`guardian_id`)
  * `class()` $\rightarrow$ `BelongsTo` ke `AcademicClass` (`class_id`)

### C. Model `AcademicYear`
* **Namespace**: `App\Modules\Academic\Models\AcademicYear`
* **Tabel**: `academic_years`
* **Fitur & Trait**: `SoftDeletes`, `HasAuditLogs`
* **Relasi**:
  * `semesters()` $\rightarrow$ `HasMany` ke `Semester` (`academic_year_id`)

### D. Model `Semester`
* **Namespace**: `App\Modules\Academic\Models\Semester`
* **Tabel**: `semesters`
* **Fitur & Trait**: `SoftDeletes`, `HasAuditLogs`
* **Relasi**:
  * `academicYear()` $\rightarrow$ `BelongsTo` ke `AcademicYear` (`academic_year_id`)
  * `classes()` $\rightarrow$ `HasMany` ke `AcademicClass` (`semester_id`)

### E. Model `AcademicClass`
* **Namespace**: `App\Modules\Academic\Models\AcademicClass`
* **Tabel**: `classes` (menghindari kata kunci cadangan PHP `class`)
* **Fitur & Trait**: `SoftDeletes`, `HasAuditLogs`
* **Relasi**:
  * `semester()` $\rightarrow$ `BelongsTo` ke `Semester` (`semester_id`)
  * `students()` $\rightarrow$ `HasMany` ke `Student` (`class_id`)

---

## 2. Peta Hubungan Relasional (Eloquent Relationship Graph)

Berikut adalah visualisasi graf relasi antarmodel yang terbentuk di level ORM Eloquent:

```text
       +-----------------------+
       |   Auth\Models\User    |
       +-----------------------+
                   | (hasOne)
                   |
                   v (belongsTo)
       +-----------------------+
       | Student\Models\       |
       |       Guardian        |
       +-----------------------+
                   | (hasMany)
                   |
                   v (belongsTo)
       +-----------------------+
       | Student\Models\       |
       |       Student         |
       +-----------------------+
                   | (belongsTo)
                   |
                   v (hasMany)
       +-----------------------+
       | Academic\Models\      |
       |     AcademicClass     |
       +-----------------------+
                   | (belongsTo)
                   |
                   v (hasMany)
       +-----------------------+
       | Academic\Models\      |
       |       Semester        |
       +-----------------------+
                   | (belongsTo)
                   |
                   v (hasMany)
       +-----------------------+
       | Academic\Models\      |
       |     AcademicYear      |
       +-----------------------+
```

---

## 3. Hasil Pengujian Verifikasi (Verification Results)

* **Namespace Checks**: Seluruh kelas model diletakkan pada domain modular monolith yang tepat (`App\Modules\Student` dan `App\Modules\Academic`), mencegah polusi pada namespace global `App\Models`.
* **Database Isomorphism**: Definisi nama tabel (`$table`), fillable array, dan casting array sinkron secara presisi dengan berkas migrasi fisik Sprint 1A.
* **Bi-directional Integrity**: Seluruh hubungan relasional dideklarasikan secara dua arah (misal: `AcademicClass` memiliki `students()`, dan `Student` memiliki `class()`), mempermudah pemuatan relasi asinkron (*eager loading*) di Laravel untuk optimasi kueri `$student->load('class.semester.academicYear')`.
* **Compiles Cleanly**: Kode bebas dari error kompilasi dan linting, siap digunakan oleh Repository dan Service Layer pada langkah selanjutnya.
