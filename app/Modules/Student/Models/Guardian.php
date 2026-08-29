<?php

namespace App\Modules\Student\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Base\Traits\HasAuditLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guardian extends Model
{
    use SoftDeletes, HasAuditLogs;

    /**
     * Nama tabel database.
     */
    protected $table = 'guardians';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'user_id',
        'guardian_name',
        'guardian_relation',
        'phone_number',
        'address',
    ];

    /**
     * Cast tipe data atribut.
     */
    protected $casts = [
        'user_id' => 'integer',
    ];

    /**
     * Relasi ke model User (Akun login wali murid).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke model Student (Siswa yang berada di bawah perwalian).
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'guardian_id');
    }
}
