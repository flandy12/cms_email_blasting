<?php

namespace App\Services;

use App\Models\User;

use function Pest\Laravel\json;

class UserService
{
    public function paginate(User $users, int $perPage = 20)
    {
        return $users->query()->paginate($perPage);
    }

    public function show($id)
    {
        // Ambil template berdasarkan ID
        $users = User::findOrFail($id);
        return $users;
    }

    public function create($request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|unique:users|string|max:255',
        ]);

        $users = User::create($request);

        return $users;
    }

    public function update($request, $id)
    {

        // Ambil user berdasarkan ID
        $users = $this->show($id);

        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
        ]);

        $users->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        return $users;
    }
}
