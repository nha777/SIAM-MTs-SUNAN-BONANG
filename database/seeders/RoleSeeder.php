<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Jalankan seed database untuk peran (roles) standar SIAM.
     */
    public function run(): void
    {
        $roles = [
            'Super Admin',
            'Bendahara',
            'Tata Usaha',
            'Kepala Madrasah',
            'Wali Kelas',
            'Orang Tua',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }
    }
}
