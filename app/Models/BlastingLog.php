<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlastingLog extends Model
{
    protected $table = 'blasting_logs';

    // karena kamu tidak pakai updated_at
    public $timestamps = false;

    protected $fillable = [
        'campaign_id',
        'email',
        'status',
        'response',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(BlastingCampaign::class, 'campaign_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES (optional tapi powerful)
    |--------------------------------------------------------------------------
    */

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
