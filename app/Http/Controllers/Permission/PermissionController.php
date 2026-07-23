<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PermissionService;
use Inertia\Inertia;

class PermissionController extends Controller
{
    public function __construct(private PermissionService $service) {}

    /* =========================
    INDEX
    ========================= */
    public function index()
    {
        return Inertia::render('permissions/Master', [
            'permissions' => $this->service->getAll()
        ]);
    }

    /* =========================
    CREATE
    ========================= */
    public function create()
    {
        return Inertia::render('permissions/partials/Create');
    }

    /* =========================
    STORE
    ========================= */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name'
        ]);

        $this->service->create($validated);

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission berhasil dibuat');
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
        $permission = $this->service->findById($id);

        return Inertia::render('permissions/Edit', [
            'permission' => $permission
        ]);
    }

    /* =========================
    UPDATE
    ========================= */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $id
        ]);

        $this->service->update($id, $validated);

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission berhasil diupdate');
    }

    /* =========================
    DELETE
    ========================= */
    public function destroy($id)
    {
        $this->service->delete($id);

        return redirect()
            ->back()
            ->with('success', 'Permission berhasil dihapus');
    }
}
