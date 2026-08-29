<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ensure roles and permissions seeded first
        $this->call([\Database\Seeders\RoleSeeder::class, \Database\Seeders\RolePermissionSeeder::class]);

        // Create an admin, staff, and parent user idempotently using the configured auth model
        $userModel = config('auth.providers.users.model');

        $userModel::firstOrCreate(
            ['email' => 'admin@siam.local'],
            ['name' => 'Admin User', 'password' => bcrypt('password123')]
        );

        $userModel::firstOrCreate(
            ['email' => 'staff@siam.local'],
            ['name' => 'Staff TU', 'password' => bcrypt('staffpass')]
        );

        $userModel::firstOrCreate(
            ['email' => 'parent@siam.local'],
            ['name' => 'Parent User', 'password' => bcrypt('parentpass')]
        );

        // Keep existing test user for compatibility
        $userModel::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );

        // Assign roles to seeded users (idempotent)
        $admin = $userModel::where('email', 'admin@siam.local')->first();
        if ($admin) {
            $admin->syncRoles(['Super Admin']);
        }

        $staff = $userModel::where('email', 'staff@siam.local')->first();
        if ($staff) {
            $staff->syncRoles(['Tata Usaha']);
        }

        $parent = $userModel::where('email', 'parent@siam.local')->first();
        if ($parent) {
            $parent->syncRoles(['Orang Tua']);
        }

        $test = $userModel::where('email', 'test@example.com')->first();
        if ($test) {
            $test->syncRoles(['Wali Kelas']);
        }
    }
}
