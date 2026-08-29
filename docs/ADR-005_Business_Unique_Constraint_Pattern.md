# ADR 005: Business Unique Constraint Pattern with Soft Deletes

## 1. Status
**Accepted**

## 2. Context
SIAM (Sistem Informasi Akademik Madrasah) is built using the Laravel framework and uses MariaDB/MySQL as the primary relational database. There are no plans to migrate to PostgreSQL. Many core entities in SIAM require both *Soft Deletes* (retaining data history/preventing accidental permanent deletion) and *Business Unique Constraints* (ensuring no duplicate active records exist, e.g., two "VII A" classes in the same Academic Year).

## 3. Problem Statement
When using *Soft Deletes* alongside a traditional unique constraint in MariaDB/MySQL, a conflict arises: if a record is soft-deleted, inserting a new record with the same unique business keys will trigger a database unique constraint violation, even though the old record is logically "deleted". 
While MariaDB/MySQL supports composite unique keys (e.g., `academic_year_id`, `grade`, `name`, `deleted_at`), `deleted_at` is usually `NULL` for active records. In standard MySQL, multiple rows with `NULL` in a unique index do not conflict, which defeats the purpose of the unique constraint for active records (unless handled specifically, which varies across MySQL/MariaDB versions and behaviors).

## 4. Decision
To enforce data integrity reliably at the database level while supporting soft deletes, SIAM will adopt the **Generated / Virtual Column Pattern** for all Business Unique Constraints.

**The Strategy:**
1. **Database Level (Source of Truth):** Combine the necessary business columns into a single generated/virtual column that only outputs a value when `deleted_at IS NULL`. A unique index is then applied to this generated column.
2. **Application Level (User Experience):** Continue using Laravel's Validation (`Rule::unique(...)->ignore(...)` or closure-based rules) to intercept duplicates early and provide user-friendly error messages before hitting the database constraint.

## 5. Alternatives Considered
* **Partial Unique Indexes:** PostgreSQL supports `CREATE UNIQUE INDEX ... WHERE deleted_at IS NULL`. However, since SIAM strictly uses MariaDB/MySQL, this feature is natively unavailable or unsupported in the same elegant manner.
* **Including `deleted_at` in Unique Index:** Because `NULL` values are generally ignored in unique indexes in MySQL (allowing multiple `NULL`s), it fails to restrict duplicate active records.
* **Relying Solely on Laravel Validation:** Relying only on application-level checks can lead to race conditions (TOCTOU - Time of Check to Time of Use) where concurrent requests might insert duplicate data before the framework can catch it.

## 6. Consequences
* **Positive:** Guaranteed data integrity at the database level, preventing race conditions from creating duplicate active records.
* **Positive:** Soft-deleted records can co-exist with new active records that share the same business keys.
* **Negative:** Slightly increased complexity in database migrations and table structure.

## 7. Implementation Standard

### A. Naming Convention
The generated column SHOULD follow a domain-specific naming convention, such as `active_class_key`, `active_subject_key`, `active_classroom_key`, or another clearly descriptive name. This ensures clarity when debugging SQL or viewing the database schema.

### B. Migration Pattern Example
When creating or altering tables that require this pattern, the generated expression MUST uniquely represent the business key. The exact expression (e.g., using `CONCAT()`) is left to the specific migration's implementation as long as it correctly represents the unique combination.

Example structure:
```php
$table->string('active_class_key')->virtualAs("CASE WHEN deleted_at IS NULL THEN /* unique expression here */ ELSE NULL END")->nullable();
$table->unique('active_class_key', 'uq_table_name_active_class_key');
```

### C. Scope
This pattern is mandatory for all current and future SIAM modules that require Business Unique Constraints alongside Soft Deletes, including but not limited to:
* Academic Class
* Subject
* Classroom
* Payment Template

### D. Future Maintenance
If the infrastructure strategy changes in the future and SIAM migrates to a DBMS that natively supports Partial Unique Indexes (such as PostgreSQL or SQLite 3.32+), this ADR can be revised, and the virtual columns can be replaced with native partial indexes.
