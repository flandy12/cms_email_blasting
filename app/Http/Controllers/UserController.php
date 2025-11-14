<?php

namespace App\Http\Controllers;

use App\Imports\UserImport;
use App\Services\MasterService;
use App\Services\UserImportService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    protected MasterService $masterService;

    public function __construct(MasterService $masterService)
    {
        $this->masterService = $masterService;
    }

    public function index()
    {
        $jobs = $this->masterService->masterBlast();
        return Inertia::render('email/Master', compact('jobs'));
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls'
        ]);

        $this->masterService->import($request->file('file'));

        return Inertia::render('email/Master');
    }

    public function deleteAllEmail()
    {
        $this->masterService->deleteDataBlast();
        return Inertia::render('email/Master');
    }

    public function messageTemplate()
    {
        return Inertia::render('template/Master');
    }

    public function messageTemplateStore()
    {
        return Inertia::render('template/partials/Create');
    }

    public function messageTemplateView()
    {
        return Inertia::render('template/partials/Edit');
    }
}
