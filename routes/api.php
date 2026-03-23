<?php

use App\Http\Controllers\Api\BlastingWebhookController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Models\BlastJob;
use Illuminate\Support\Facades\Route;

Route::post('/campaign/email-status', [BlastingWebhookController::class, 'updateStatus']);

