<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Jalankan seed database untuk peran (roles) dan izin (permissions) standar SIAM.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Daftar izin (permissions)
        $permissions = [


            'student.view',
            'student.create',
            'student.update',
            'student.delete',
            'student.restore',

            'guardian.view',
            'guardian.create',
            'guardian.update',
            'guardian.delete',

            'academic_year.view',
            'academic_year.create',
            'academic_year.update',
            'academic_year.delete',
            'academic_year.restore',
            'academic_year.activate',

            'semester.view',
            'semester.create',
            'semester.update',
            'semester.delete',
            'semester.restore',
            'semester.activate',

            'class.view',
            'class.create',
            'class.update',
            'class.delete',
            'class.restore',
            'user.view',
            'user.create',
            'user.update',
            'user.delete',
            'user.restore',
            'role.view',
            'role.create',
            'role.update',
            'role.delete',
            'permission.view',
            'activity_log.view',
            
            'employee.view', 'employee.create', 'employee.update', 'employee.delete',
            'subject.view', 'subject.create', 'subject.update', 'subject.delete',
    
        
            'academic.view', 'academic.create', 'academic.update', 'academic.delete',
            'finance.view', 'finance.create', 'finance.update', 'finance.delete',
    
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Daftar peran (roles)
        $roles = [
            'Super Admin',
            'Bendahara',
            'Tata Usaha',
            'Kepala Madrasah',
            'Wali Kelas',
            'Orang Tua',
        ];

        $roleInstances = [];
        foreach ($roles as $roleName) {
            $roleInstances[$roleName] = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }

        // Pemetaan awal izin ke peran:

        // 1. Super Admin: Semua permission
        $roleInstances['Super Admin']->syncPermissions(Permission::all());

        // 2. Tata Usaha: student.*, guardian.*, academic_year (view, create, update), semester (view, create, update), class (view, create, update, restore)
        $roleInstances['Tata Usaha']->syncPermissions([
            'student.view',
            'student.create',
            'student.update',
            'student.delete',
            'student.restore',
            'guardian.view',
            'guardian.create',
            'guardian.update',
            'guardian.delete',
            'academic_year.view',
            'academic_year.create',
            'academic_year.update',
            'semester.view',
            'semester.create',
            'semester.update',
            'class.view',
            'class.create',
            'class.update',
            'class.restore',
            ]);

        // 3. Wali Kelas: student.view, class.view
        $roleInstances['Wali Kelas']->syncPermissions([
            'student.view',
            'class.view',
        ]);

        // 4. Bendahara: student.view, guardian.view
        $roleInstances['Bendahara']->syncPermissions([
            'student.view',
            'guardian.view',
        ]);

        // 5. Kepala Madrasah: Semua yang bersifat '.view' (Read-Only)
        $roleInstances['Kepala Madrasah']->syncPermissions([
            'student.view',
            'guardian.view',
            'academic_year.view',
            'semester.view',
            'class.view',
        ]);

        // 6. Orang Tua: Tidak mendapat permission administratif global (diperiksa via policy/kepemilikan)
        $roleInstances['Orang Tua']->syncPermissions([]);
    }
}
