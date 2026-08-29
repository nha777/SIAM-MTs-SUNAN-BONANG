<?php

namespace App\Modules\Academic\Models;

use App\Modules\Base\Traits\HasAuditLogs;
use App\Modules\Student\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicClass extends Model
{
    use SoftDeletes, HasAuditLogs;

    /**
     * Nama tabel database.
     */
    protected $table = 'classes';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'academic_year_id',
        'semester_id',
        'name',
        'grade',
        'capacity',
        'display_order',
    ];

    /**
     * Cast tipe data atribut.
     */
    protected $casts = [
        'academic_year_id' => 'integer',
        'semester_id' => 'integer',
        'grade' => 'integer',
        'capacity' => 'integer',
        'display_order' => 'integer',
    ];

    /**
     * Mendapatkan string romawi untuk grade/tingkat kelas
     */
    public static function getRomanGrade(int $grade): string
    {
        $mapping = [
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
        ];

        return $mapping[$grade] ?? (string) $grade;
    }

    /**
     * Accessor untuk nama lengkap kelas (misal: "VII A")
     */
    public function getFullNameAttribute(): string
    {
        return self::getRomanGrade($this->grade) . ' ' . $this->name;
    }

    /**
     * Relasi ke model AcademicYear.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    /**
     * Relasi kompatibilitas lama ke semester.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Academic\Models\Semester::class, 'semester_id');
    }

    /**
     * Relasi ke model Student (Siswa yang terdaftar di kelas ini).
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }
}
