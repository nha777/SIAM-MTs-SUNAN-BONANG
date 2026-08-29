<?php

namespace App\Modules\Base\Models;

use Illuminate\Database\Eloquent\Model;
use Exception;

class AuditLog extends Model
{
    /**
     * Nama tabel database.
     */
    protected $table = 'audit_logs';

    /**
     * Atribut tidak bisa diisi secara massal (semua guarded)
     * Karena log audit hanya dibuat via Event & Job.
     */
    protected $guarded = ['*'];

    public $timestamps = false; // Karena kita menggunakan created_at secara native default CURRENT_TIMESTAMP

    /**
     * Cast tipe data atribut.
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Boot model untuk memastikan sifat Read-Only (Immutability).
     */
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            throw new Exception("Audit Log Immutability Violation: Cannot update an audit log record.");
        });

        static::deleting(function ($model) {
            throw new Exception("Audit Log Immutability Violation: Cannot delete an audit log record.");
        });
    }
}
