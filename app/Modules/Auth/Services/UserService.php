<?php

namespace App\Modules\Auth\Services;

use App\Modules\Base\Services\BaseService;
use App\Modules\Auth\Repositories\Contracts\UserRepositoryInterface;
use App\Modules\Auth\Services\Contracts\UserServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserService extends BaseService implements UserServiceInterface
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        parent::__construct($userRepository);
        $this->userRepository = $userRepository;
    }

    public function store(array $data): \Illuminate\Database\Eloquent\Model
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        DB::beginTransaction();
        try {
            $user = parent::store($data);
            
            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }
            
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int|string $id, array $data): bool
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']); // Don't update if not provided
        }
        
        DB::beginTransaction();
        try {
            $status = parent::update($id, $data);
            
            if (isset($data['roles'])) {
                $user = $this->getById($id);
                $user->syncRoles($data['roles']);
            }
            
            DB::commit();
            return $status;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function setStatus(int|string $id, bool $isActive): bool
    {
        return $this->update($id, ['is_active' => $isActive]);
    }
}
