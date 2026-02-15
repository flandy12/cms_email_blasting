<?php

namespace App\Services;
use App\Models\BlastingTemplate;

class TemplateService
{
    public function index()
    {
        $data = BlastingTemplate::orderBy('created_at', 'DESC')->paginate(10);
        return $data;
    }
    public function store($request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $results = BlastingTemplate::create([
            'name' => $request->name,
            'subject' => $request->subject,
            'body' => $request->body,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return $results;
    }

    public function edit($id)
    {
        $template = BlastingTemplate::findOrFail($id);
        return $template;
    }

    public function update($request, $id)
    {
        $template = BlastingTemplate::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $template->update([
            'name' => $request->name,
            'subject' => $request->subject,
            'body' => $request->body,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return $template;
    }

    public function delete($id)
    {
        $template = BlastingTemplate::findOrFail($id);
        $template->delete();
        return;
    }

}
