<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlastingTemplate extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'wording',
        'button_type',
        'button_text',
        'url_type',
        'url',
        'params',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'params' => 'array',
        'is_active' => 'boolean',
    ];
}
