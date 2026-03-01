<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Models\BlastingCampaign;
use App\Services\BlastingCampaignService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BlastingCampaignController extends Controller
{
    protected BlastingCampaignService $service;

    public function __construct(BlastingCampaignService $service)
    {
        $this->service = $service;
    }

    /* =========================
       INDEX
    ========================= */
    public function index()
    {
        $campaigns = $this->service->paginate(10);

        return Inertia::render('blastingCampaign/Master', [
            'campaigns' => $campaigns,
        ]);
    }

    /* =========================
       CREATE
    ========================= */
    public function create()
    {
        return Inertia::render('blastingCampaign/partials/Create', [
            'templates' => $this->service->getActiveTemplates(),
        ]);
    }

    /* =========================
       STORE
    ========================= */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'template_id' => ['required', 'exists:blasting_templates,id'],
            'scheduled_at' => ['nullable', 'date'],
        ]);

        $this->service->create($validated, Auth::id());

        return redirect()
            ->route('blasting.campaigns.index')
            ->with('success', 'Campaign berhasil dibuat');
    }

    /* =========================
       SHOW
    ========================= */
    public function show($id)
    {
        $blastingCampaign = $this->service->findOrFail($id);
        return Inertia::render('blastingCampaign/partials/Edit', [
            'campaign'  => $blastingCampaign->load('template'),
            'templates' => $this->service->getActiveTemplates(),
        ]);
    }

    /* =========================
       UPDATE
    ========================= */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'template_id' => ['required', 'exists:blasting_templates,id'],
            'scheduled_at' => ['nullable', 'date'],
        ]);

        $blastingCampaign = $this->service->findOrFail($id);
        $this->service->update($blastingCampaign, $validated);

        return redirect()
            ->route('blasting.campaigns.index')
            ->with('success', 'Campaign berhasil diperbarui');
    }

    /* =========================
       PAUSE
    ========================= */
    public function pause(BlastingCampaign $blastingCampaign)
    {
        abort_if(
            $blastingCampaign->status !== 'running',
            422,
            'Campaign tidak sedang berjalan'
        );

        $blastingCampaign->update([
            'status' => 'paused'
        ]);

        return back()->with('success', 'Campaign berhasil dipause');
    }

    /* =========================
       RESUME
    ========================= */
    public function resume(BlastingCampaign $blastingCampaign)
    {
        abort_if(
            $blastingCampaign->status !== 'paused',
            422,
            'Campaign tidak dalam kondisi pause'
        );

        $blastingCampaign->update([
            'status' => 'running'
        ]);

        // Optional: dispatch job blasting di sini
        // dispatch(new ProcessBlastingCampaign($blastingCampaign));

        return back()->with('success', 'Campaign dilanjutkan');
    }

    /* =========================
       CANCEL
    ========================= */
    public function cancel(BlastingCampaign $blastingCampaign)
    {
        abort_if(
            in_array($blastingCampaign->status, ['completed', 'cancelled']),
            422,
            'Campaign sudah selesai'
        );

        $blastingCampaign->update([
            'status' => 'cancelled'
        ]);

        return back()->with('success', 'Campaign dibatalkan');
    }

    public function destroyAll()
    {
        $this->service->deleteAll();
        return redirect()->route('blasting.campaigns.index');
    }
}
