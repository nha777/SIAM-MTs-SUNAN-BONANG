<?php

namespace App\Modules\Academic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Subject extends Model
{
    use SoftDeletes, HasUuids;

    protected $table = 'subjects';

    protected $fillable = [
        'code',
        'name',
        'type',
        'credits',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credits' => 'integer',
    ];
}
