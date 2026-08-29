<?php

namespace App\Modules\Academic\Policies;

use App\Modules\Auth\Models\User;
use App\Modules\Academic\Models\AcademicYear;
use Illuminate\Auth\Access\HandlesAuthorization;

class AcademicYearPolicy
{
    use HandlesAuthorization;

    /**
     * Tentukan apakah pengguna dapat melihat daftar tahun ajaran.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('academic_year.view') || $user->hasPermissionTo('academic.view');
    }

    /**
     * Tentukan apakah pengguna dapat melihat detail tahun ajaran tertentu.
     */
    public function view(User $user, AcademicYear $academicYear): bool
    {
        return $user->hasPermissionTo('academic_year.view') || $user->hasPermissionTo('academic.view');
    }

    /**
     * Tentukan apakah pengguna dapat membuat tahun ajaran baru.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('academic_year.create') || $user->hasPermissionTo('academic.create');
    }

    /**
     * Tentukan apakah pengguna dapat memperbarui data tahun ajaran.
     */
    public function update(User $user, AcademicYear $academicYear): bool
    {
        return $user->hasPermissionTo('academic_year.update') || $user->hasPermissionTo('academic.update');
    }

    /**
     * Tentukan apakah pengguna dapat menghapus data tahun ajaran.
     */
    public function delete(User $user, AcademicYear $academicYear): bool
    {
        return $user->hasPermissionTo('academic_year.delete') || $user->hasPermissionTo('academic.delete');
    }

    /**
     * Tentukan apakah pengguna dapat memulihkan data tahun ajaran.
     */
    public function restore(User $user, AcademicYear $academicYear): bool
    {
        return $user->hasPermissionTo('academic_year.restore') || $user->hasPermissionTo('academic.delete');
    }

    /**
     * Tentukan apakah pengguna dapat mengaktifkan tahun ajaran tertentu.
     */
    public function activate(User $user, AcademicYear $academicYear): bool
    {
        return $user->hasPermissionTo('academic_year.activate') || $user->hasPermissionTo('academic.update');
    }
}
