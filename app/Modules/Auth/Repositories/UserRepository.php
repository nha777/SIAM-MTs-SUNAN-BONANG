<?php

namespace App\Modules\Auth\Repositories;

use App\Modules\Base\Repositories\BaseRepository;
use App\Modules\Auth\Repositories\Contracts\UserRepositoryInterface;
use App\Modules\Auth\Models\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * UserRepository constructor.
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Cari pengguna aktif berdasarkan alamat email.
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->where('is_active', true)->first();
    }
}
