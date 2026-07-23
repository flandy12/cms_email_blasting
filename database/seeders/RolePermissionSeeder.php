<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Ambil role
        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $owner = Role::firstOrCreate([
            'name' => 'owner',
            'guard_name' => 'web',
        ]);

        $sales = Role::firstOrCreate([
            'name' => 'sales',
            'guard_name' => 'web',
        ]);

        // Ambil seluruh permission
        $permissions = Permission::all();

        // Admin mendapatkan semua permission
        $admin->syncPermissions($permissions);

        // Owner
        $owner->syncPermissions([
            'dashboard.view',
            'template-campaign.view',
            'recipients.view',
            'recipients.export',
            'schedule.view',
            'users.view',
            'roles.view',
            'permissions.view',
            'logs.view',
        ]);

        // Sales
        $sales->syncPermissions([
            'dashboard.view',

            'template-campaign.view',
            'template-campaign.create',
            'template-campaign.edit',
            'template-campaign.delete',

            'recipients.view',
            'recipients.create',
            'recipients.edit',
            'recipients.delete',
            'recipients.import',
            'recipients.export',

            'schedule.view',
            'schedule.create',
            'schedule.edit',
            'schedule.delete',
            'schedule.run',
            'schedule.pause',

            'logs.view',
        ]);

        // Reset cache setelah assign permission
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}