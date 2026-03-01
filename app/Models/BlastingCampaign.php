<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlastingCampaign extends Model
{
    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'template_id',
        'name',
        'scheduled_at',
        'status',
        'total_recipient',
        'sent_count',
        'failed_count',
        'created_by',
    ];
    /**
     * Cast attributes
     */
    protected $casts = [
        'scheduled_at' => 'datetime',
        'total_recipient' => 'integer',
        'sent_count' => 'integer',
        'failed_count' => 'integer',
    ];

    /**
     * Default attribute values
     */
    protected $attributes = [
        'status' => 'draft',
        'total_recipient' => 0,
        'sent_count' => 0,
        'failed_count' => 0,
    ];

    /* =========================
       RELATIONSHIPS
    ========================= */

    /**
     * Campaign belongs to a template
     */
    public function template()
    {
        return $this->belongsTo(BlastingTemplate::class, 'template_id');
    }

    /**
     * Campaign creator (user)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* =========================
       SCOPES
    ========================= */

    /**
     * Scope draft campaigns
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope running campaigns
     */
    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    /**
     * Scope finished campaigns
     */
    public function scopeFinished($query)
    {
        return $query->where('status', 'finished');
    }

    /* =========================
       HELPERS
    ========================= */

    /**
     * Check if campaign can be started
     */
    public function canStart(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if campaign can be paused
     */
    public function canPause(): bool
    {
        return $this->status === 'running';
    }

    /**
     * Check if campaign can be resumed
     */
    public function canResume(): bool
    {
        return $this->status === 'paused';
    }

    public function recipients()
    {
        return $this->belongsToMany(
            BlastingRecipient::class,
            'blasting_campaign_recipient',
            'campaign_id',   // FK ke campaign di pivot
            'recipient_id'   // FK ke recipient di pivot
        )->withPivot(['status', 'sent_at', 'error_message'])
            ->withTimestamps();
    }
}
