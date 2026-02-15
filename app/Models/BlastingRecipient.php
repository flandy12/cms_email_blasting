<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlastingRecipient extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'email',
        'name',
        'metadata',
        'is_active',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Default attribute values
     */
    protected $attributes = [
        'is_active' => true,
    ];

    /* =========================
       SCOPES
    ========================= */

    /**
     * Only active recipients
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Only inactive recipients (unsubscribed / blacklisted)
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /* =========================
       HELPERS
    ========================= */

    /**
     * Check recipient is active
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Disable recipient (unsubscribe / bounce)
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Enable recipient again
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Get metadata value safely
     */
    public function meta(string $key, $default = null)
    {
        return data_get($this->metadata, $key, $default);
    }
}
