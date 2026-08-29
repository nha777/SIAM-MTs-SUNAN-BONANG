<?php

namespace App\Modules\Auth\Services\Contracts;

use App\Modules\Base\Contracts\BaseServiceInterface;

interface UserServiceInterface extends BaseServiceInterface
{
    /**
     * Set active status for a user.
     */
    public function setStatus(int|string $id, bool $isActive): bool;
}
