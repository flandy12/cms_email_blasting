<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function paginate(User $users, int $perPage = 20)
    {
        return $users->query()->paginate($perPage);
    }

    public function edit($id)
    {
        // Ambil template berdasarkan ID
        $users = User::findOrFail($id);
        return $users;
    }
}
