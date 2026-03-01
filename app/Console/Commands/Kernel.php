<?php

namespace App\Console;

use App\Console\Commands\BlastingCampaign\RunBlastingCampaign;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<int, class-string>
     */
    protected $commands = [
      RunBlastingCampaign::class
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan setiap menit
        $schedule->command('campaign:run')
            ->everyMinute()

            // Hindari double execution
            ->withoutOverlapping()

            // Jalankan di background
            ->runInBackground()

            // Set timezone (WAJIB kalau server UTC)
            ->timezone('Asia/Jakarta')

            // Logging
            ->appendOutputTo(storage_path('logs/campaign.log'))

            // Hanya jalan di production
            ->environments(['local', 'production']);
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}