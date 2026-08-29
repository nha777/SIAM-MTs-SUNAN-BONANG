<?php

namespace App\Modules\Academic\Policies;

use App\Modules\Auth\Models\User;
use App\Modules\Academic\Models\Semester;
use Illuminate\Auth\Access\HandlesAuthorization;

class SemesterPolicy
{
    use HandlesAuthorization;

    /**
     * Tentukan apakah pengguna dapat melihat daftar semester.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('semester.view') || $user->hasPermissionTo('academic.view');
    }

    /**
     * Tentukan apakah pengguna dapat melihat detail semester tertentu.
     */
    public function view(User $user, Semester $semester): bool
    {
        return $user->hasPermissionTo('semester.view') || $user->hasPermissionTo('academic.view');
    }

    /**
     * Tentukan apakah pengguna dapat membuat semester baru.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('semester.create') || $user->hasPermissionTo('academic.create');
    }

    /**
     * Tentukan apakah pengguna dapat memperbarui semester tertentu.
     */
    public function update(User $user, Semester $semester): bool
    {
        return $user->hasPermissionTo('semester.update') || $user->hasPermissionTo('academic.update');
    }

    /**
     * Tentukan apakah pengguna dapat menghapus semester tertentu.
     */
    public function delete(User $user, Semester $semester): bool
    {
        return $user->hasPermissionTo('semester.delete') || $user->hasPermissionTo('academic.delete');
    }

    /**
     * Tentukan apakah pengguna dapat memulihkan semester tertentu.
     */
    public function restore(User $user, Semester $semester): bool
    {
        return $user->hasPermissionTo('semester.restore') || $user->hasPermissionTo('academic.delete');
    }

    /**
     * Tentukan apakah pengguna dapat mengaktifkan semester tertentu.
     */
    public function activate(User $user, Semester $semester): bool
    {
        return $user->hasPermissionTo('semester.activate') || $user->hasPermissionTo('academic.update');
    }
}
