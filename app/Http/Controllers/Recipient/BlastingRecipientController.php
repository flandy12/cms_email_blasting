<?php

namespace App\Http\Controllers\Recipient;

use App\Http\Controllers\Controller;
use App\Models\BlastingRecipient;
use App\Services\BlastingRecipientService;
use App\Imports\BlastingRecipientImport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class BlastingRecipientController extends Controller
{
    protected BlastingRecipientService $service;

    public function __construct(BlastingRecipientService $service)
    {
        $this->service = $service;
    }

    /**
     * =========================
     * INDEX
     * =========================
     */
    public function index(Request $request)
    {
        $recipients = $this->service->paginate(
            $request->search,
            15
        );

        return Inertia::render('recipient/Master', [
            'recipients' => $recipients,
            'filters'    => $request->only('search'),
        ]);
    }

    /**
     * =========================
     * CREATE
     * =========================
     */
    public function create()
    {
        return Inertia::render('recipient/partials/Create');
    }

    /**
     * =========================
     * STORE
     * =========================
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email'     => ['required', 'email', 'max:255', 'unique:blasting_recipients,email'],
            'name'      => ['nullable', 'string', 'max:150'],
            'metadata'  => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $this->service->create($validated);

        return redirect()
            ->route('blasting.recipients.index')
            ->with('success', 'Recipient berhasil ditambahkan');
    }

    /**
     * =========================
     * Show
     * =========================
     */
    public function edit($id)
    {
        $recipients = $this->service->findOrFail($id);
        return Inertia::render('recipient/partials/Edit', [
            'recipients' => $recipients,
        ]);
    }

    /**
     * =========================
     * UPDATE
     * =========================
     */
    public function update(Request $request, BlastingRecipient $recipient)
    {
        $validated = $request->validate([
            'email'     => ['required', 'email', 'max:255', "unique:blasting_recipients,email,{$recipient->id}"],
            'name'      => ['nullable', 'string', 'max:150'],
            'metadata'  => ['nullable', 'array'],
            'is_active' => ['required', 'boolean'],
        ]);

        $this->service->update($recipient, $validated);

        return redirect()
            ->route('blasting.recipients.index')
            ->with('success', 'Recipient berhasil diperbarui');
    }

    /**
     * =========================
     * DELETE
     * =========================
     */
    public function destroy(BlastingRecipient $recipient)
    {
        $this->service->delete($recipient);

        return redirect()
            ->route('blasting.recipients.index')
            ->with('success', 'Recipient berhasil dihapus');
    }

    /**
     * =========================
     * TOGGLE STATUS
     * =========================
     */
    public function toggleStatus(BlastingRecipient $recipient)
    {
        $this->service->toggleStatus($recipient);

        return back()->with('success', 'Status recipient berhasil diubah');
    }

    /**
     * =========================
     * IMPORT EXCEL
     * =========================
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
        ]);

        Excel::import(new BlastingRecipientImport, $request->file('file'));

        return back()->with('success', 'Data berhasil diimport');
    }
}
