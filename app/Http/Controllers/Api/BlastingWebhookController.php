<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlastingCampaign;
use App\Models\BlastingCampaignRecipient;
use App\Models\BlastingLog;
use App\Models\BlastingRecipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BlastingWebhookController extends Controller
{
    public function updateStatus(Request $request)
    {

        if ($request->header('X-Webhook-Secret') !== config('services.n8n.key')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'campaign_id'   => ['required', 'integer', 'exists:blasting_campaigns,id'],
            'email'         => ['required', 'email'],
            'status'        => ['required', Rule::in(['sent', 'failed'])],
            'recipient_id'  => ['required'],
            'response'      => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($data) {

            // 🔒 Lock campaign
            $campaign = BlastingCampaign::where('id', $data['campaign_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // 🔍 Ambil log existing (lock juga biar aman race condition)
            $existingLog = BlastingLog::where('campaign_id', $campaign->id)
                ->where('email', $data['email'])
                ->lockForUpdate()
                ->first();

            $isAlreadySuccess = $existingLog &&
                $existingLog->status === 'sent';

            // 🚫 Jika sudah sukses sebelumnya → skip increment
            if (!$isAlreadySuccess) {

                if ($data['status'] === 'sent') {

                    if (!$existingLog || $existingLog->status !== 'sent') {

                        if ($existingLog && $existingLog->status === 'failed') {
                            $campaign->decrement('failed_count');
                        }

                        $campaign->increment('sent_count');
                    }
                } else {

                    if (!$existingLog || $existingLog->status !== 'failed') {
                        $campaign->increment('failed_count');
                    }
                }
            }

            // 📝 Upsert log
            BlastingLog::updateOrCreate(
                [
                    'campaign_id' => $campaign->id,
                    'email'       => $data['email'],
                ],
                [
                    'status'   => $data['status'],
                    'response' => $data['response'] ?? null,
                    'created_at'  => now(),
                ]
            );

            BlastingCampaignRecipient::updateOrCreate(
                [
                    'campaign_id' => $campaign->id,
                    'recipient_id' => $data['recipient_id'],
                ],
                [
                    'status' => $data['status'],
                    'sent_at' => now(),
                    'error_message' => $data['response'] ?? null,
                ]
            );

            BlastingRecipient::where('email', $data['email'])->update(
                ['is_active' => 0]
            );

            // 🏁 Auto finish
            if (
                $campaign->total_recipient > 0 &&
                ($campaign->sent_count + $campaign->failed_count) >= $campaign->total_recipient
            ) {
                $campaign->update(['status' => 'finished']);
            }

            return response()->json([
                'success' => true,
                'message' => $isAlreadySuccess
                    ? 'Already recorded as success, no increment'
                    : 'Status updated',
                'data' => [
                    'campaign_id' => $campaign->id,
                    'email'       => $data['email'] ?? null,
                    'status'      => $data['status'] ?? null,
                ]
            ], 200);
        });
    }
}
