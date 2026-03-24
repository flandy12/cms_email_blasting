<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class RoleService
{
    /**
     * Get all roles with permissions
     */
    public function getAll()
    {
        return Role::with('permissions')->get();
    }

    /**
     * Get single role (for edit)
     */
    public function findById(int $id)
    {
        return Role::with('permissions')->findOrFail($id);
    }

    /**
     * Create new role
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'web'
            ]);

            if (!empty($data['permissions'])) {
                $permissions = Permission::whereIn('name', $data['permissions'])->pluck('name');
                $role->syncPermissions($permissions);
            }

            $this->resetCache();

            return $role->load('permissions');
        });
    }

    /**
     * Update role
     */
    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {

            $role = Role::findOrFail($id);

            // 🔐 Prevent critical role update
            if ($role->name === 'super-admin') {
                throw new \Exception('Cannot modify super-admin role');
            }

            $role->update([
                'name' => $data['name']
            ]);

            $role->syncPermissions($data['permissions'] ?? []);

            $this->resetCache();

            return $role->load('permissions');
        });
    }

    /**
     * Delete role
     */
    public function delete(int $id)
    {
        return DB::transaction(function () use ($id) {

            $role = Role::findOrFail($id);

            // 🔐 Protect critical role
            if ($role->name === 'super-admin') {
                throw new \Exception('Cannot delete super-admin role');
            }

            $role->delete();

            $this->resetCache();

            return true;
        });
    }

    /**
     * Reset Spatie cache
     */
    protected function resetCache()
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
