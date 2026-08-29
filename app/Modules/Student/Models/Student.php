<?php

namespace App\Modules\Student\Models;

use App\Modules\Academic\Models\AcademicClass;
use App\Modules\Base\Traits\HasAuditLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;
    use SoftDeletes, HasAuditLogs;

    /**
     * Nama tabel database.
     */
    protected $table = 'students';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'guardian_id',
        'class_id',
        'nisn',
        'name',
        'gender',
        'birth_place',
        'birth_date',
        'status',
    ];

    /**
     * Cast tipe data atribut.
     */
    protected $casts = [
        'guardian_id' => 'integer',
        'class_id' => 'integer',
        'birth_date' => 'date',
    ];

    /**
     * Relasi ke model Guardian (Wali murid).
     */
    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class, 'guardian_id');
    }

    /**
     * Relasi ke model AcademicClass (Rombongan belajar kelas).
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }

    /**
     * Backwards-compatible alias for academic class relation.
     */
    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }
}
