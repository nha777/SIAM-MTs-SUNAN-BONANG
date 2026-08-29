<?php

namespace App\Modules\Academic\Policies;

use App\Modules\Auth\Models\User;
use App\Modules\Academic\Models\AcademicClass;
use Illuminate\Auth\Access\HandlesAuthorization;

class AcademicClassPolicy
{
    use HandlesAuthorization;

    /**
     * Tentukan apakah pengguna dapat melihat daftar kelas.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('class.view') || $user->hasPermissionTo('academic.view');
    }

    /**
     * Tentukan apakah pengguna dapat melihat detail kelas tertentu.
     */
    public function view(User $user, AcademicClass $academicClass): bool
    {
        return $user->hasPermissionTo('class.view') || $user->hasPermissionTo('academic.view');
    }

    /**
     * Tentukan apakah pengguna dapat membuat kelas baru.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('class.create') || $user->hasPermissionTo('academic.create');
    }

    /**
     * Tentukan apakah pengguna dapat memperbarui data kelas.
     */
    public function update(User $user, AcademicClass $academicClass): bool
    {
        return $user->hasPermissionTo('class.update') || $user->hasPermissionTo('academic.update');
    }

    /**
     * Tentukan apakah pengguna dapat menghapus data kelas.
     */
    public function delete(User $user, AcademicClass $academicClass = null): bool
    {
        return $user->hasPermissionTo('class.delete') || $user->hasPermissionTo('academic.delete');
    }

    /**
     * Tentukan apakah pengguna dapat mengembalikan data kelas.
     */
    public function restore(User $user, AcademicClass $academicClass = null): bool
    {
        return $user->hasPermissionTo('class.restore') || $user->hasPermissionTo('academic.delete');
    }
}
