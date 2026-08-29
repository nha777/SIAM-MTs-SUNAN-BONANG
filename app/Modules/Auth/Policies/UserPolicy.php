<?php

namespace App\Modules\Auth\Policies;

use App\Modules\Auth\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('user.view');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasPermissionTo('user.view') || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('user.create');
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasPermissionTo('user.update') || $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->hasPermissionTo('user.delete');
    }

    public function restore(User $user, User $model): bool
    {
        return $user->hasPermissionTo('user.restore');
    }
}
