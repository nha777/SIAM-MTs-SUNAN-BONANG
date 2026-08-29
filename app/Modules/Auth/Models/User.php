<?php

namespace App\Modules\Auth\Models;

use App\Modules\Base\Traits\HasAuditLogs;
use Database\Factories\Modules\Auth\Models\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, HasAuditLogs, SoftDeletes, Notifiable;

    /**
     * Nama tabel database.
     */
    protected $table = 'users';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    /**
     * Atribut yang disembunyikan dalam serialisasi JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Kolom mutasi tanggal.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    /**
     * Relasi ke model Guardian (Wali murid).
     */
    public function guardian(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Modules\Student\Models\Guardian::class, 'user_id');
    }
}
