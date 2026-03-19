<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlastingCampaignRecipient extends Model
{
    protected $fillable = [
        'campaign_id',
        'recipient_id',
        'status',
        'sent_at',
        'error_message',
    ];
}
