<?php

namespace App\Modules\Student\Policies;

use App\Modules\Auth\Models\User;
use App\Modules\Student\Models\Student;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentPolicy
{
    use HandlesAuthorization;

    /**
     * Tentukan apakah pengguna dapat melihat daftar siswa.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('student.view');
    }

    /**
     * Tentukan apakah pengguna dapat melihat detail siswa tertentu.
     */
    public function view(User $user, Student $student): bool
    {
        return $user->hasPermissionTo('student.view');
    }

    /**
     * Tentukan apakah pengguna dapat membuat siswa baru.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('student.create');
    }

    /**
     * Tentukan apakah pengguna dapat memperbarui data siswa.
     */
    public function update(User $user, Student $student): bool
    {
        return $user->hasPermissionTo('student.update');
    }

    /**
     * Tentukan apakah pengguna dapat menghapus data siswa.
     */
    public function delete(User $user, Student $student): bool
    {
        return $user->hasPermissionTo('student.delete');
    }

    /**
     * Tentukan apakah pengguna dapat memulihkan data siswa yang dihapus lunak.
     */
    public function restore(User $user, Student $student): bool
    {
        return $user->hasPermissionTo('student.restore');
    }
}
