<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear permission cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'users.create',
            'users.edit',
            'users.delete',
            'users.view',
            'view.dashboard'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $merchantRole = Role::firstOrCreate(['name' => 'merchant']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        $adminRole->syncPermissions($permissions);

        $merchantRole->syncPermissions([
            'users.view',
            'view.dashboard'
        ]);

        $userRole->syncPermissions([
            'users.view',
            'view.dashboard'
        ]);
    }
}
