<?php

namespace App\Services;

use App\Models\BlastingCampaign;
use App\Models\BlastingCampaignRecipient;
use App\Models\BlastingRecipient;
use App\Models\BlastingTemplate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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

            // Create campaign
            $campaign = BlastingCampaign::create([
                'name'         => $data['name'],
                'template_id'  => $data['template_id'],
                'scheduled_at' => $data['scheduled_at'] ?? now(),
                'created_by'   => $userId,
            ]);

            // Ambil semua recipient aktif
            $recipients = BlastingRecipient::where('is_active', true)
                ->whereNotNull('email')
                ->get(['id']);

            if ($recipients->isEmpty()) {
                return $campaign; // Tidak ada recipient
            }

            // Siapkan pivot data
            $pivotData = [];

            foreach ($recipients as $recipient) {
                $pivotData[$recipient->id] = [
                    'status'        => 'pending',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }

            // Attach ke pivot
            $campaign->recipients()->attach($pivotData);

            // Update total recipient
            $campaign->update([
                'total_recipient' => count($pivotData)
            ]);

            return $campaign;
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
        return DB::transaction(function () use ($campaign, $data) {

            $scheduledAt = $campaign->scheduled_at
                ? Carbon::parse($campaign->scheduled_at)
                : null;

            $isRunningToday = $scheduledAt
                && $scheduledAt->isToday()
                && in_array($campaign->status, ['running', 'processing']);

            $alreadyExecuted = $campaign->sent_count > 0;

            // Normalisasi scheduled_at
            if (array_key_exists('scheduled_at', $data)) {
                $data['scheduled_at'] = $data['scheduled_at']
                    ? Carbon::parse($data['scheduled_at'])->format('Y-m-d H:i:s')
                    : null;
            }

            /**
             * 🔁 CASE 1: Sudah jalan / sedang jalan → clone campaign
             */
            if ($alreadyExecuted || $isRunningToday) {

                $newCampaignData = array_merge(
                    $campaign->only([
                        'template_id',
                        'name',
                        'scheduled_at',
                        'status',
                        'total_recipient'
                    ]),
                    $data
                );

                $newCampaignData['status'] = 'scheduled';
                $newCampaignData['sent_count'] = 0;
                $newCampaignData['failed_count'] = 0;
                $newCampaignData['created_by'] = Auth::id();

                $newCampaign = BlastingCampaign::create($newCampaignData);

                // 🔥 Insert recipients dengan chunk + bulk insert
                BlastingRecipient::select('id')
                    ->chunkById(500, function ($recipients) use ($newCampaign) {

                        $insertData = [];

                        foreach ($recipients as $recipient) {
                            $insertData[] = [
                                'campaign_id' => $newCampaign->id,
                                'recipient_id' => $recipient->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }

                        DB::table('blasting_campaign_recipients')->insert($insertData);
                    });

                return $newCampaign;
            }

            /**
             * 🔁 CASE 2: Belum jalan → update langsung
             */
            $campaign->update($data);

            // 🔥 sync recipients (optional: truncate dulu biar tidak duplicate)
            DB::table('blasting_campaign_recipients')
                ->where('campaign_id', $campaign->id)
                ->delete();

            BlastingRecipient::select('id')
                ->chunkById(500, function ($recipients) use ($campaign) {

                    $insertData = [];

                    foreach ($recipients as $recipient) {
                        $insertData[] = [
                            'campaign_id' => $campaign->id,
                            'recipient_id' => $recipient->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    DB::table('blasting_campaign_recipients')->insert($insertData);
                });

            return $campaign;
        });
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

            DB::table('blasting_campaign_recipients')->delete();

            DB::table('blasting_campaigns')->delete();
        });

        return response()->json([
            'message' => 'All campaigns deleted'
        ]);
    }
}
