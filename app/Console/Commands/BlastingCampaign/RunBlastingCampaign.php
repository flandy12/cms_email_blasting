<?php

namespace App\Console\Commands\BlastingCampaign;

use App\Models\BlastingCampaign;
use App\Models\BlastingRecipient;
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

        Log::info('Scheduler triggered at ' . $now);

        $campaigns = BlastingCampaign::with('template')
            ->where('status', 'draft')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', $now)
            ->get();

        Log::info('Campaign count: ' . $campaigns->count());

        if ($campaigns->isEmpty()) {
            return Command::SUCCESS;
        }

        foreach ($campaigns as $campaign) {

            $campaign->update(['status' => 'running']);

            $recipientTotal = BlastingRecipient::whereNotNull('email')->count();

            Log::info("Recipient total: {$recipientTotal}");

            if ($recipientTotal === 0) {
                $campaign->update(['status' => 'finished']);
                continue;
            }

            $hasError = false;

            BlastingRecipient::whereNotNull('email')
                ->orderBy('id')
                ->chunkById(500, function ($recipients) use ($campaign, &$hasError) {

                    Log::info("Sending chunk of {$recipients->count()} recipients");

                    $payload = [
                        'campaign_id' => $campaign->id,
                        'name'        => $campaign->name,
                        'template' => [
                            'id'      => $campaign->template->id ?? null,
                            'name'    => $campaign->template->name ?? null,
                            'subject' => $campaign->template->subject ?? null,
                            'body'    => $campaign->template->wording ?? null,
                            'parameters' => $campaign->template->params ?? [],
                            'url'     => $campaign->template->url ?? null,
                        ],
                        'scheduled_at' => optional($campaign->scheduled_at)?->toISOString(),
                        'recipients'  => $recipients->map(fn($r) => [
                            'id'    => (int) $r->id,
                            'name'  => (string) $r->name,
                            'email' => (string) $r->email,
                        ])->values()->toArray()
                    ];

                    try {

                        $response = Http::timeout(30)
                            ->retry(2, 1000)
                            ->post(config('services.n8n.webhook_url'), $payload);

                        Log::info('n8n response', [
                            'status' => $response->status()
                        ]);

                        if (!$response->successful()) {
                            $hasError = true;
                            return false;
                        }
                    } catch (\Throwable $e) {

                        $hasError = true;

                        Log::error('n8n connection error', [
                            'message' => $e->getMessage(),
                        ]);

                        return false;
                    }
                });

            $campaign->update([
                'status' => $hasError ? 'paused' : 'finished'
            ]);
        }

        return Command::SUCCESS;
    }
}
