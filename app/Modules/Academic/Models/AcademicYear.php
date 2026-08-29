<?php

namespace App\Modules\Academic\Models;

use App\Modules\Base\Traits\HasAuditLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicYear extends Model
{
    use SoftDeletes, HasAuditLogs;

    /**
     * Nama tabel database.
     */
    protected $table = 'academic_years';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'name',
        'start_year',
        'end_year',
        'is_active',
    ];

    /**
     * Cast tipe data atribut.
     */
    protected $casts = [
        'start_year' => 'integer',
        'end_year' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke model Semester.
     */
    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class, 'academic_year_id');
    }

    /**
     * Relasi ke model AcademicClass.
     */
    public function classes(): HasMany
    {
        return $this->hasMany(AcademicClass::class, 'academic_year_id');
    }
}
