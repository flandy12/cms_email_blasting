<?php

namespace App\Services;

use App\Models\BlastingTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TemplateService
{
    public function index()
    {
        $data = BlastingTemplate::orderBy('created_at', 'DESC')->paginate(10);
        return $data;
    }
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'subject'     => 'required|string|max:255',
            'wording'     => 'required|string',
            'button_type' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'url_type'    => 'nullable|string',
            'url'         => 'nullable|string',
            'params'      => 'nullable|array',
        ]);

        // Simpan template ke database
        BlastingTemplate::create([
            'name'        => $validated['name'],
            'subject'     => $validated['subject'],
            'wording'     => $validated['wording'],
            'button_type' => $validated['button_type'] ?? null,
            'button_text' => $validated['button_text'] ?? null,
            'url_type'    => $validated['url_type'] ?? null,
            'url'         => $validated['url'] ?? null,
            'params'      => $validated['params'] ?? null,
            'is_active'   => true,
            'created_by'  => auth()->id(),
        ]);

        return redirect()
            ->route('templates.index')
            ->with('success', 'Template berhasil dibuat.');
    }

    public function edit($id)
    {
        // Ambil template berdasarkan ID
        $template = BlastingTemplate::findOrFail($id);
        return $template;
    }

    public function update($request, $id)
    {
        // Ambil template berdasarkan ID
        $template = BlastingTemplate::findOrFail($id);

        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        // Update template di database
        $template->update([
            'name' => $request->name,
            'subject' => $request->subject,
            'body' => $request->body,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return $template;
    }

    public function destroyAll()
    {
        // Hapus semua template dari database
        DB::table('blasting_templates')->delete();
        return [];
    }
}
