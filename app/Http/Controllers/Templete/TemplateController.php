<?php

namespace App\Http\Controllers\Templete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TemplateService;
use Inertia\Inertia;

class TemplateController extends Controller
{
    protected TemplateService $templateService;

    public function __construct(TemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    public function index()
    {
        $templates = $this->templateService->index();
        return Inertia('template/Master',compact('templates'));
    }

    public function create(){
        return Inertia('template/partials/Create');
    }

    public function store(Request $request){
        $this->templateService->store($request);
        return redirect()->route('templates.index');
    }

    public function edit($id){
        $template = $this->templateService->edit($id);
        return Inertia('template/partials/Edit', compact('template'));
    }

    public function update(Request $request, $id){
        $this->templateService->update($request, $id);
        return redirect()->route('templates.index');
    }

    public function destroyAll(){
        $this->templateService->destroyAll();
        return redirect()->route('templates.index')->with('success', 'Semua template berhasil dihapus');
    }

}