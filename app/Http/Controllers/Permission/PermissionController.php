<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PermissionService;

class PermissionController extends Controller
{
    public function __construct(private PermissionService $service) {}

    public function index()
    {
        return response()->json($this->service->getAll());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name'
        ]);

        return response()->json($this->service->create($validated));
    }

    public function destroy($id)
    {
        return response()->json([
            'status' => $this->service->delete($id)
        ]);
    }
}
