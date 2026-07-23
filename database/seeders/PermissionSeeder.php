<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [

            // Dashboard
            'dashboard.view',

            // Template Campaign
            'template-campaign.view',
            'template-campaign.create',
            'template-campaign.edit',
            'template-campaign.delete',

            // Recipients
            'recipients.view',
            'recipients.create',
            'recipients.edit',
            'recipients.delete',
            'recipients.import',
            'recipients.export',

            // Schedule List
            'schedule.view',
            'schedule.create',
            'schedule.edit',
            'schedule.delete',
            'schedule.run',
            'schedule.pause',

            // Users
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Roles
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // Permissions
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',

            // Logs
            'logs.view',
            'logs.delete',
            'logs.retry',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }
}