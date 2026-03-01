<?php

namespace App\Console\Commands\BlastingCampaign;

use App\Models\BlastingCampaign;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class RunBlastingCampaign extends Command
{
    protected $signature = 'campaign:run';
    protected $description = 'Run scheduled blasting campaigns';

    public function handle()
    {
        $now = Carbon::now();

        $campaigns = BlastingCampaign::where('status', 'draft')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', $now)
            ->get();

        foreach ($campaigns as $campaign) {

            // Update status ke running
            $campaign->update([
                'status' => 'running'
            ]);

            // Trigger n8n webhook
            Http::post(config('services.n8n.webhook_url'), [
                'campaign_id' => $campaign->id,
                'template_id' => $campaign->template_id,
                'name' => $campaign->name
            ]);
        }

        return Command::SUCCESS;
    }
}
