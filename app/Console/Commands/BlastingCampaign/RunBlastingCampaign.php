<?php

namespace App\Console\Commands\BlastingCampaign;

use App\Models\BlastingCampaign;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RunBlastingCampaign extends Command
{
    protected $signature = 'campaign:run';
    protected $description = 'Run scheduled blasting campaigns';

    public function handle()
    {
        DB::beginTransaction();

        try {

            $now = Carbon::now();

            // Ambil campaign yang siap jalan + lock supaya tidak double
            $campaigns = BlastingCampaign::where('status', 'draft')
                ->whereNotNull('scheduled_at')
                ->where('scheduled_at', '<=', $now)
                ->lockForUpdate()
                ->get();

            if ($campaigns->isEmpty()) {
                $this->info('No campaign ready.');
                DB::commit();
                return Command::SUCCESS;
            }

            foreach ($campaigns as $campaign) {

                $this->info("Processing Campaign ID: {$campaign->id}");

                // Update ke running
                $campaign->update([
                    'status' => 'running'
                ]);

                // Kirim recipients per chunk (hindari memory overload)
                $campaign->recipients()
                    ->select('id', 'name', 'phone')
                    ->whereNotNull('phone')
                    ->chunk(500, function ($recipients) use ($campaign) {

                        $payload = [
                            'campaign_id' => $campaign->id,
                            'name'        => $campaign->name,
                            'template_id' => $campaign->template_id,
                            'scheduled_at'=> $campaign->scheduled_at,
                            'recipients'  => $recipients->map(function ($r) {
                                return [
                                    'id'    => $r->id,
                                    'name'  => $r->name,
                                    'phone' => $r->phone,
                                ];
                            })->values()
                        ];

                        $response = Http::timeout(30)
                            ->retry(2, 1000) // retry 2x delay 1 detik
                            ->post(config('services.n8n.webhook_url'), $payload);

                        if (!$response->successful()) {

                            Log::error('n8n webhook failed', [
                                'campaign_id' => $campaign->id,
                                'status'      => $response->status(),
                                'response'    => $response->body(),
                            ]);

                            throw new \Exception('Webhook failed');
                        }
                    });

                // Jika semua chunk sukses
                $campaign->update([
                    'status' => 'sent'
                ]);
            }

            DB::commit();

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Campaign scheduler error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            $this->error($e->getMessage());
        }

        return Command::SUCCESS;
    }
}