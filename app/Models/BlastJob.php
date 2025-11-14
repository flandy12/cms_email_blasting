<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlastJob extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'status'
    ];
}
