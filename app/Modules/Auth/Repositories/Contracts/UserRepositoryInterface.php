<?php

namespace App\Modules\Auth\Repositories\Contracts;

use App\Modules\Base\Contracts\BaseRepositoryInterface;
use App\Modules\Auth\Models\User;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Cari pengguna aktif berdasarkan alamat email.
     */
    public function findByEmail(string $email): ?User;
}
