<?php

namespace App\Console\Commands\BlastingCampaign;

use App\Models\BlastingCampaign;
use App\Models\BlastingRecipient;
use App\Models\BlastingLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RunBlastingCampaign extends Command
{
    protected $signature = 'campaign:run';
    protected $description = 'Run scheduled blasting campaigns';

    public function handle()
    {
        $now = now();

        Log::info('Campaign scheduler triggered', ['time' => $now]);

        $campaigns = BlastingCampaign::with('template')
            ->where('status', 'draft')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', $now)
            ->get();

        if ($campaigns->isEmpty()) {
            return Command::SUCCESS;
        }

        foreach ($campaigns as $campaign) {

            // 🔒 Lock campaign
            $updated = BlastingCampaign::where('id', $campaign->id)
                ->where('status', 'draft')
                ->update(['status' => 'running']);

            if (!$updated) {
                continue;
            }

            $recipientQuery = BlastingRecipient::whereNotNull('email');

            $total = $recipientQuery->count();

            $campaign->update([
                'total_recipient' => $total
            ]);

            if ($total === 0) {
                $campaign->update(['status' => 'finished']);
                continue;
            }

            $success = 0;
            $failed  = 0;

            $recipientQuery
                ->orderBy('id')
                ->chunkById(500, function ($recipients) use ($campaign, &$success, &$failed) {

                    // KIRIM SEKALIGUS (ARRAY)
                    $payload = $recipients->map(function ($recipient) use ($campaign) {
                        return [
                            'campaign_id' => $campaign->id,
                            'name' => $campaign->name,
                            'template' => [
                                'subject' => $campaign->template->subject ?? null,
                                'body' => $campaign->template->wording ?? null,
                            ],
                            'recipient' => [
                                'id' => (int) $recipient->id,
                                'name' => $recipient->name,
                                'email' => $recipient->email,
                            ]
                        ];
                    })->values()->toArray();

                    try {

                        $response = Http::timeout(60)
                            ->retry(2, 1000)
                            ->post(config('services.n8n.webhook_url'), $payload);

                        if ($response->successful()) {

                            $success += count($payload);

                            $logs = collect($payload)->map(function ($item) use ($campaign) {
                                return [
                                    'campaign_id' => $campaign->id,
                                    'email' => $item['recipient']['email'],
                                    'status' => 'sent',
                                    'response' => 'queued to n8n',
                                    'created_at' => now()
                                ];
                            })->toArray();

                        } else {

                            $failed += count($payload);

                            $logs = collect($payload)->map(function ($item) use ($campaign, $response) {
                                return [
                                    'campaign_id' => $campaign->id,
                                    'email' => $item['recipient']['email'],
                                    'status' => 'failed',
                                    'response' => $response->body(),
                                    'created_at' => now()
                                ];
                            })->toArray();
                        }

                    } catch (\Throwable $e) {

                        $failed += count($payload);

                        Log::error('Chunk send error', [
                            'message' => $e->getMessage()
                        ]);

                        $logs = collect($payload)->map(function ($item) use ($campaign, $e) {
                            return [
                                'campaign_id' => $campaign->id,
                                'email' => $item['recipient']['email'],
                                'status' => 'failed',
                                'response' => $e->getMessage(),
                                'created_at' => now()
                            ];
                        })->toArray();
                    }

                    if (!empty($logs)) {
                        BlastingLog::insert($logs);
                    }

                    // ⚡ Optional: delay antar chunk
                    usleep(500000); // 0.5 detik
                });

            $campaign->update([
                'status' => 'finished',
                'sent_count' => $success,
                'failed_count' => $failed
            ]);

            Log::info("Campaign finished", [
                'campaign_id' => $campaign->id,
                'success' => $success,
                'failed' => $failed
            ]);
        }

        return Command::SUCCESS;
    }
}