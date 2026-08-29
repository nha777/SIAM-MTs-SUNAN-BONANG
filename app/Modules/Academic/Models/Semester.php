<?php

namespace App\Modules\Academic\Models;

use App\Modules\Base\Traits\HasAuditLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Semester extends Model
{
    use SoftDeletes, HasAuditLogs;

    /**
     * Nama tabel database.
     */
    protected $table = 'semesters';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'academic_year_id',
        'semester',
        'name',
        'is_active',
    ];

    /**
     * Cast tipe data atribut.
     */
    protected $casts = [
        'academic_year_id' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke model AcademicYear (Tahun ajaran induk).
     */
    protected static function booted(): void
    {
        static::saving(function (self $semester) {
            if (empty($semester->semester) && !empty($semester->name)) {
                $semester->semester = strtolower((string) $semester->name);
            }

            if (empty($semester->name) && !empty($semester->semester)) {
                $semester->name = ucfirst((string) $semester->semester);
            }

            if (!empty($semester->semester) && !empty($semester->name)) {
                $semester->name = ucfirst((string) $semester->semester);
            }
        });
    }

    public function getNameAttribute($value): ?string
    {
        if (!is_null($value)) {
            return $value;
        }

        return !empty($this->semester) ? ucfirst((string) $this->semester) : null;
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = $value;

        if (empty($this->attributes['semester']) && !empty($value)) {
            $this->attributes['semester'] = strtolower((string) $value);
        }
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    /**
     * Relasi ke model AcademicClass (Daftar kelas di semester ini).
     */
    public function classes(): HasMany
    {
        return $this->hasMany(AcademicClass::class, 'semester_id');
    }
}
