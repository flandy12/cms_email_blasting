<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RoleService;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct(private RoleService $service) {}

    /* =========================
    INDEX
    ========================= */
    public function index()
    {
        return Inertia::render('roles/Master', [
            'roles' => $this->service->getAll()
        ]);
    }

    /* =========================
    CREATE
    ========================= */
    public function create()
    {
        return Inertia::render('roles/partials/Create', [
            'permissions' => Permission::select('id', 'name')->get()
        ]);
    }

    /* =========================
    STORE
    ========================= */
    public function store(Request $request)
    {
    
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $this->service->create($validated);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil dibuat');
    }

    /* =========================
    SHOW (optional)
    ========================= */
    public function show($id)
    {
        return response()->json(
            $this->service->findById($id)
        );
    }

    /* =========================
    EDIT
    ========================= */
    public function edit($id)
    {
        $role = $this->service->findById($id);

        return Inertia::render('roles/Edit', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name')
            ],
            'permissions' => Permission::select('id', 'name')->get()
        ]);
    }

    /* =========================
    UPDATE
    ========================= */
    public function update(Request $request, $id)
    {
        // 🔐 Authorization
        if (!auth()->user()->can('edit role')) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $this->service->update($id, $validated);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil diupdate');
    }

    /* =========================
    DELETE
    ========================= */
    public function destroy($id)
    {
        // 🔐 Authorization
        if (!auth()->user()->can('delete role')) {
            abort(403);
        }

        $this->service->delete($id);

        return redirect()
            ->back()
            ->with('success', 'Role berhasil dihapus');
    }
}