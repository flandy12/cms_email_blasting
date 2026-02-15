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
        $data = $this->templateService->store($request);

        dd($data);
        return redirect()->route('template');
    }

    public function edit($id){
        $template = $this->templateService->edit($id);
        return Inertia('template/partials/Edit', compact('template'));
    }

    public function update(Request $request, $id){
        $this->templateService->update($request, $id);
        return redirect()->route('template');
    }

    public function delete($id){
        $this->templateService->delete($id);
        return redirect()->route('template');
    }
}
