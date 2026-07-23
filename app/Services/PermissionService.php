<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionService
{
    public function getAll()
    {
        return Permission::all();
    }

    public function create(array $data)
    {
        return Permission::create([
            'name' => $data['name'],
            'guard_name' => 'web'
        ]);
    }

    public function delete(int $id)
    {
        return Permission::findOrFail($id)->delete();
    }

    public function findById(int $id)
    {
        return Permission::findOrFail($id);
    }

    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {

            $permission = Permission::findOrFail($id);

            $permission->update([
                'name' => $data['name']
            ]);

            $this->resetCache();

            return $permission;
        });
    }
}
