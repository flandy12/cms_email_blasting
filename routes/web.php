<?php

use App\Http\Controllers\BlastingCampaignRecipient\BlastingCampaignRecipientController;
use App\Http\Controllers\Campaign\BlastingCampaignController;
use App\Http\Controllers\EmailSchedule\EmailScheduleController;
use App\Http\Controllers\Recipient\BlastingRecipientController;
use App\Http\Controllers\Templete\TemplateController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('/token', function () {
    return response()->json([
        'token' => encrypt(env('TOKEN_API')),
    ]);
})->name('token');


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', fn() => Inertia::render('Dashboard'))
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Template Management
    |--------------------------------------------------------------------------
    */
    Route::delete('templates/destroy-all', [TemplateController::class, 'destroyAll'])
    ->name('templates.destroyAll');

    Route::resource('templates', TemplateController::class)
        ->except(['show']);

    /*
    |--------------------------------------------------------------------------
    | Email Schedule
    |--------------------------------------------------------------------------
    */
    Route::resource('schedules', EmailScheduleController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::prefix('schedules/{schedule}')
        ->name('schedules.')
        ->group(function () {
            Route::put('/pause', [EmailScheduleController::class, 'pause'])
                ->name('pause');

            Route::put('/resume', [EmailScheduleController::class, 'resume'])
                ->name('resume');

            Route::delete('/', [EmailScheduleController::class, 'cancel'])
                ->name('cancel');
        });


    /*
    |--------------------------------------------------------------------------
    | Blasting Module
    |--------------------------------------------------------------------------
    */
    Route::prefix('blasting')
        ->name('blasting.')
        ->group(function () {

            /*
        |--------------------------------------------------------------------------
        | Campaigns
        |--------------------------------------------------------------------------
        */

        // Custom route harus di atas resource supaya tidak bentrok
        Route::delete('campaigns/destroy-all', [
            BlastingCampaignController::class,
            'destroyAll'
        ])->name('campaigns.destroyAll');

        // Resource route
        Route::resource('campaigns', BlastingCampaignController::class)
            ->names('campaigns');

        // Action tambahan per campaign
        Route::prefix('campaigns/{campaign}')
            ->name('campaigns.')
            ->group(function () {

                Route::patch('pause', [
                    BlastingCampaignController::class,
                    'pause'
                ])->name('pause');

                Route::patch('resume', [
                    BlastingCampaignController::class,
                    'resume'
                ])->name('resume');

                Route::patch('cancel', [
                    BlastingCampaignController::class,
                    'cancel'
                ])->name('cancel');
            });

            /*
        |--------------------------------------------------------------------------
        | Global Recipients
        |--------------------------------------------------------------------------
        */
            
            Route::delete(
                'recipients/destroy-all',
                [BlastingRecipientController::class, 'destroyAll']
            )->name('recipients.destroyAll');
         
            Route::resource('recipients', BlastingRecipientController::class)->name('*', 'recipients');

            Route::post(
                'recipients/import',
                [BlastingRecipientController::class, 'import']
            )->name('recipients.import');

            Route::patch(
                'recipients/{recipient}/toggle',
                [BlastingRecipientController::class, 'toggleStatus']
            )->name('recipients.toggle');



            /*
        |--------------------------------------------------------------------------
        | Campaign Recipients (Nested)
        |--------------------------------------------------------------------------
        */
            Route::prefix('campaigns/{campaign}')
                ->scopeBindings()
                ->name('campaigns.')
                ->group(function () {

                    Route::get(
                        'recipients',
                        [BlastingCampaignRecipientController::class, 'index']
                    )->name('recipients.index');

                    Route::post(
                        'recipients',
                        [BlastingCampaignRecipientController::class, 'store']
                    )->name('recipients.store');

                    Route::delete(
                        'recipients/{recipient}',
                        [BlastingCampaignRecipientController::class, 'destroy']
                    )->name('recipients.destroy');

                    Route::post(
                        'recipients/{recipient}/retry',
                        [BlastingCampaignRecipientController::class, 'retry']
                    )->name('recipients.retry');
                });
        });


    /*
    |--------------------------------------------------------------------------
    | Log
    |--------------------------------------------------------------------------
    */
    Route::get('/logs', function () {
        return Inertia::render('Logs');
    })->name('log.index');

   Route::get('/test', function () {
        Http::post(config('services.n8n.webhook_url'), [
            'message' => 'Hello n8n!',
            'timestamp' => now()->toDateTimeString(),
        ]);
        return 'Webhook sent!';
    })->name('test');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/api.php';
