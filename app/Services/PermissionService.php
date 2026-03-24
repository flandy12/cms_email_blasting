<?php

namespace App\Services;

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
}
