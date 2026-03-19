<?php

namespace App\Services;

use App\Models\BlastingCampaign;
use App\Models\BlastingRecipient;
use Illuminate\Support\Facades\DB;

class BlastingCampaignRecipientService
{
    /*
    |--------------------------------------------------------------------------
    | PAGINATE RECIPIENTS PER CAMPAIGN
    |--------------------------------------------------------------------------
    */
    public function paginate(BlastingCampaign $campaign, int $perPage = 20)
    {
        return $campaign->recipients()
            ->select([
                'blasting_recipients.id',
                'blasting_recipients.name',
                'blasting_recipients.phone',
                'blasting_campaign_recipients.status',
            ])
            ->paginate($perPage);
    }

    /*
    |--------------------------------------------------------------------------
    | ATTACH RECIPIENTS
    |--------------------------------------------------------------------------
    */
    public function attachRecipients(
        BlastingCampaign $campaign,
        array $recipientIds
    ): void {
        DB::transaction(function () use ($campaign, $recipientIds) {

            $syncData = collect($recipientIds)
                ->mapWithKeys(fn ($id) => [
                    $id => ['status' => 'pending']
                ])
                ->toArray();

            // Tidak detach yang lama
            $campaign->recipients()->syncWithoutDetaching($syncData);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | DETACH RECIPIENT
    |--------------------------------------------------------------------------
    */
    public function detachRecipient(
        BlastingCampaign $campaign,
        BlastingRecipient $recipient
    ): void {

        if (!$campaign->recipients()->where('blasting_recipients.id', $recipient->id)->exists()) {
            throw new \Exception('Recipient tidak terdaftar di campaign ini');
        }

        $campaign->recipients()->detach($recipient->id);
    }

    /*
    |--------------------------------------------------------------------------
    | RETRY RECIPIENT
    |--------------------------------------------------------------------------
    */
    public function retryRecipient(
        BlastingCampaign $campaign,
        BlastingRecipient $recipient
    ): void {

        if (!$campaign->recipients()->where('blasting_recipients.id', $recipient->id)->exists()) {
            throw new \Exception('Recipient tidak terdaftar di campaign ini');
        }

        $campaign->recipients()->updateExistingPivot(
            $recipient->id,
            [
                'status'     => 'pending',
                'updated_at' => now(),
            ]
        );
    }
}
