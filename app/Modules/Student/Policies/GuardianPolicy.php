<?php

namespace App\Modules\Student\Policies;

use App\Modules\Auth\Models\User;
use App\Modules\Student\Models\Guardian;
use Illuminate\Auth\Access\HandlesAuthorization;

class GuardianPolicy
{
    use HandlesAuthorization;

    /**
     * Tentukan apakah pengguna dapat melihat daftar wali murid.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('guardian.view');
    }

    /**
     * Tentukan apakah pengguna dapat melihat detail wali murid tertentu.
     */
    public function view(User $user, Guardian $guardian): bool
    {
        return $user->hasPermissionTo('guardian.view');
    }

    /**
     * Tentukan apakah pengguna dapat membuat wali murid baru.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('guardian.create');
    }

    /**
     * Tentukan apakah pengguna dapat memperbarui data wali murid.
     */
    public function update(User $user, Guardian $guardian): bool
    {
        return $user->hasPermissionTo('guardian.update');
    }

    /**
     * Tentukan apakah pengguna dapat menghapus data wali murid.
     */
    public function delete(User $user, Guardian $guardian): bool
    {
        return $user->hasPermissionTo('guardian.delete');
    }

    /**
     * Tentukan apakah pengguna dapat memulihkan data wali murid.
     */
    public function restore(User $user, Guardian $guardian): bool
    {
        return $user->hasPermissionTo('guardian.restore');
    }
}
