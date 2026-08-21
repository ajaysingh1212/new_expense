<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SuperAdminSecuritySeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $superAdmin = Role::updateOrCreate(
            ['name' => 'super-admin', 'guard_name' => 'web'],
            [
                'description' => 'Super Administrator with full security and system access',
                'color' => '#dc3545',
                'icon' => 'fas fa-crown',
                'is_default' => false,
            ]
        );

        $superAdmin->syncPermissions(Permission::all());

        $user = User::updateOrCreate(
            ['email' => 'superadmin@rbac.com'],
            [
                'name' => 'Super Administrator',
                'username' => 'superadmin',
                'password' => bcrypt('password'),
                'phone' => '+91 9876543210',
                'designation' => 'System Super Administrator',
                'department' => 'IT',
                'bio' => 'Full system access account.',
                'is_active' => true,
                'created_by' => null,
                'allow_mobile_login' => true,
                'allow_desktop_login' => true,
                'trusted_ip_only' => false,
                'max_active_devices' => null,
            ]
        );

        $user->syncRoles([$superAdmin]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command?->info('Super admin role/user seeded.');
        $this->command?->table(
            ['Role', 'Email', 'Password'],
            [['Super Admin', 'superadmin@rbac.com', 'password']]
        );
    }
}
