<?php

namespace App\Services;

use App\Models\BlastingCampaign;
use App\Models\BlastingTemplate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlastingCampaignService
{
    /* =========================
       INDEX
    ========================= */
    public function paginate(int $perPage = 10)
    {
        return BlastingCampaign::query()
            ->with('template:id,name,subject')
            ->latest()
            ->paginate($perPage);
    }

    /* =========================
       CREATE
    ========================= */
    public function create(array $data, int $userId): BlastingCampaign
    {
        return DB::transaction(function () use ($data, $userId) {

            return BlastingCampaign::create([
                'name'            => $data['name'],
                'template_id'     => $data['template_id'],
                'scheduled_at'     => $data['scheduled_at'] ?? now(),
                'status'          => 'draft',
                'total_recipient' => 0,
                'sent_count'      => 0,
                'failed_count'    => 0,
                'created_by'      => $userId,
            ]);
        });
    }

    /* =========================
       FIND
    ========================= */
    public function findOrFail(int $id): BlastingCampaign
    {
        return BlastingCampaign::with('template')
            ->findOrFail($id);
    }

    /* =========================
       UPDATE
    ========================= */
    public function update(BlastingCampaign $campaign, array $data): BlastingCampaign
    {
        if (array_key_exists('scheduled_at', $data)) {

            $data['scheduled_at'] = $data['scheduled_at']
                ? Carbon::parse($data['scheduled_at'])->format('Y-m-d H:i:s')
                : null;
        }

        $campaign->update($data);

        return $campaign;
    }

    /* =========================
       DELETE
    ========================= */
    public function delete(BlastingCampaign $campaign): void
    {
        if ($campaign->status === 'running') {
            throw new \Exception('Campaign sedang berjalan');
        }

        $campaign->delete();
    }

    /* =========================
       ACTIVE TEMPLATES
    ========================= */
    public function getActiveTemplates()
    {
        return BlastingTemplate::query()
            ->where('is_active', true)
            ->select('id', 'name')
            ->get();
    }

    public function deleteAll()
    {
        DB::transaction(function () {

            DB::table('blasting_campaign_recipient')->delete();

            DB::table('blasting_campaigns')->delete();
        });

        return response()->json([
            'message' => 'All campaigns deleted'
        ]);
    }
}
